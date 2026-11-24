<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Core\Validator;
use App\Repositories\DocumentRepository;

final class DocumentController
{
    private DocumentRepository $docs;
    private View $view;

    public function __construct()
    {
        $this->docs = new DocumentRepository();
        $this->view = new View(dirname(__DIR__, 2) . '/views');
    }

    public function index(): void
    {
        $filters = [
            'q'      => trim((string) ($_GET['q'] ?? '')),
            'type'   => trim((string) ($_GET['type'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'from'   => trim((string) ($_GET['from'] ?? '')),
            'to'     => trim((string) ($_GET['to'] ?? '')),
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $result = $this->docs->paginate($filters, $page);
        echo $this->view->render('documents/index', [
            'title'   => 'Ҳуҷҷатҳо',
            'result'  => $result,
            'filters' => $filters,
            'msg'     => $_GET['msg'] ?? null,
        ]);
    }

    public function show(int $id): void
    {
        $doc = $this->findOrFail($id);
        echo $this->view->render('documents/show', [
            'title' => $doc['number'] . ' — ' . $doc['subject'],
            'doc'   => $doc,
            'msg'   => $_GET['msg'] ?? null,
        ]);
    }

    public function create(): void
    {
        echo $this->view->render('documents/form', [
            'title'  => 'Бақайдгирии ҳуҷҷат',
            'doc'    => [
                'number'     => $this->docs->nextNumber(),
                'doc_date'   => date('Y-m-d'),
                'importance' => 'normal',
                'status'     => 'registered',
            ],
            'id'     => null,
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        $input = $_POST;
        $data = $this->normalize($input);
        $errors = $this->validate($data);
        if ($errors !== []) {
            http_response_code(422);
            echo $this->view->render('documents/form', [
                'title'  => 'Бақайдгирии ҳуҷҷат',
                'doc'    => $input,
                'id'     => null,
                'errors' => $errors,
            ]);
            return;
        }
        $id = $this->docs->create($data, 1);
        $this->redirect('/documents/' . $id . '?msg=created');
    }

    public function edit(int $id): void
    {
        $doc = $this->findOrFail($id);
        echo $this->view->render('documents/form', [
            'title'  => 'Таҳрири ҳуҷҷат',
            'doc'    => $doc,
            'id'     => $id,
            'errors' => [],
        ]);
    }

    public function update(int $id): void
    {
        $this->findOrFail($id);
        $input = $_POST;
        $data = $this->normalize($input);
        $errors = $this->validate($data);
        if ($errors !== []) {
            http_response_code(422);
            echo $this->view->render('documents/form', [
                'title'  => 'Таҳрири ҳуҷҷат',
                'doc'    => array_merge($input, ['id' => $id]),
                'id'     => $id,
                'errors' => $errors,
            ]);
            return;
        }
        $this->docs->update($id, $data);
        $this->redirect('/documents/' . $id . '?msg=updated');
    }

    public function delete(int $id): void
    {
        $this->findOrFail($id);
        $this->docs->delete($id);
        $this->redirect('/documents?msg=deleted');
    }

    private function findOrFail(int $id): array
    {
        $doc = $this->docs->find($id);
        if ($doc === null) {
            http_response_code(404);
            echo $this->view->render('errors/error', [
                'title'   => 'Ёфт нашуд',
                'code'    => 404,
                'message' => 'Ҳуҷҷат ёфт нашуд.',
            ]);
            exit;
        }
        return $doc;
    }

    private function normalize(array $input): array
    {
        $data = [];
        foreach (DocumentRepository::FIELDS as $field) {
            if ($field === 'scan_file') {
                continue;
            }
            $data[$field] = isset($input[$field]) ? trim((string) $input[$field]) : '';
        }
        return $data;
    }

    private function validate(array $data): array
    {
        $v = new Validator($data);
        $v->required('number', 'Рақам')->maxLength('number', 50, 'Рақам')
          ->required('doc_date', 'Сана')->date('doc_date', 'Сана')
          ->required('subject', 'Мавзӯъ')->minLength('subject', 4, 'Мавзӯъ')->maxLength('subject', 255, 'Мавзӯъ')
          ->required('type', 'Намуд')->in('type', ['incoming', 'outgoing', 'internal'], 'Намуд')
          ->required('importance', 'Муҳимӣ')->in('importance', ['low', 'normal', 'high'], 'Муҳимӣ')
          ->required('status', 'Ҳолат')->in('status', ['registered', 'review', 'in_progress', 'approved', 'rejected', 'done'], 'Ҳолат');
        return $v->errors();
    }

    private function redirect(string $url): void
    {
        header('Location: ' . $url, true, 302);
        exit;
    }
}
