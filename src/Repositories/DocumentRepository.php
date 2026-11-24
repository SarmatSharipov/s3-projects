<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class DocumentRepository
{
    public const FIELDS = [
        'number', 'doc_date', 'subject', 'type', 'importance',
        'sender', 'receiver', 'scan_file', 'status',
    ];

    private const SELECT = '
        SELECT d.*,
               u.full_name AS creator_name
        FROM documents d
        JOIN users u ON u.id = d.created_by';

    private PDO $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::connection();
    }

    private function buildWhere(array $filters): array
    {
        $conditions = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $conditions[] = '(d.number LIKE :q OR d.subject LIKE :q OR d.sender LIKE :q)';
            $params[':q'] = '%' . addcslashes($q, '%_\\') . '%';
        }
        if (!empty($filters['type']) && in_array($filters['type'], ['incoming', 'outgoing', 'internal'], true)) {
            $conditions[] = 'd.type = :type';
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['status']) && in_array($filters['status'], ['registered', 'review', 'in_progress', 'approved', 'rejected', 'done'], true)) {
            $conditions[] = 'd.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['from'])) {
            $conditions[] = 'd.doc_date >= :from';
            $params[':from'] = $filters['from'];
        }
        if (!empty($filters['to'])) {
            $conditions[] = 'd.doc_date <= :to';
            $params[':to'] = $filters['to'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }

    public function paginate(array $filters, int $page, int $perPage = 10): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM documents d {$where}");
        $stmt->execute($params);
        $total = (int) $stmt->fetchColumn();

        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);

        $sql = self::SELECT . " {$where} ORDER BY d.doc_date DESC, d.id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        return [
            'items'   => $items,
            'total'   => $total,
            'page'    => $page,
            'pages'   => $pages,
            'perPage' => $perPage,
        ];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(self::SELECT . ' WHERE d.id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data, int $createdBy): int
    {
        $data = $this->onlyFields($data);
        $data['created_by'] = $createdBy;
        if (!isset($data['status']) || $data['status'] === null) {
            $data['status'] = 'registered';
        }
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn ($k) => ':' . $k, array_keys($data)));
        $stmt = $this->db->prepare("INSERT INTO documents ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data = $this->onlyFields($data);
        if ($data === []) {
            return;
        }
        $sets = implode(', ', array_map(fn ($k) => "{$k} = :{$k}", array_keys($data)));
        $data['id'] = $id;
        $stmt = $this->db->prepare("UPDATE documents SET {$sets} WHERE id = :id");
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM documents WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    public function nextNumber(): string
    {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT number FROM documents WHERE number LIKE :pref ORDER BY id DESC LIMIT 1");
        $stmt->execute([':pref' => $year . '-%']);
        $last = $stmt->fetchColumn();
        $n = 1;
        if ($last && preg_match('/-(\d+)$/', (string) $last, $m)) {
            $n = (int) $m[1] + 1;
        }
        return $year . '-' . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }

    public function countByStatus(): array
    {
        $rows = $this->db->query('SELECT status, COUNT(*) AS cnt FROM documents GROUP BY status')->fetchAll();
        $result = array_fill_keys(['registered', 'review', 'in_progress', 'approved', 'rejected', 'done'], 0);
        foreach ($rows as $r) {
            $result[$r['status']] = (int) $r['cnt'];
        }
        return $result;
    }

    public function recent(int $limit = 6): array
    {
        $stmt = $this->db->prepare(self::SELECT . ' ORDER BY d.id DESC LIMIT :lim');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function statusReport(): array
    {
        $sql = "
            SELECT type,
                   COUNT(*) AS total,
                   SUM(CASE WHEN status = 'registered' THEN 1 ELSE 0 END) AS registered,
                   SUM(CASE WHEN status = 'review' THEN 1 ELSE 0 END) AS review,
                   SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress,
                   SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
                   SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
                   SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) AS done
            FROM documents
            GROUP BY type
            ORDER BY type
        ";
        return $this->db->query($sql)->fetchAll();
    }

    private function onlyFields(array $data): array
    {
        $result = [];
        foreach (self::FIELDS as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }
            $value = $data[$field];
            $result[$field] = ($value === '' || $value === null) ? null : $value;
        }
        return $result;
    }
}
