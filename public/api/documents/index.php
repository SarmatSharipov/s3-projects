<?php
// public/api/documents/index.php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

$documents = [
    [
        'id' => 41,
        'reg_number' => 'ВР-2026/041',
        'title' => 'Дархости таъмини дафтарӣ',
        'doc_type' => 'Воридотӣ',
        'sender' => 'Назарова М.',
        'doc_date' => '2026-11-20',
        'status' => 'review',
    ],
    [
        'id' => 18,
        'reg_number' => 'СД-2026/018',
        'title' => 'Ҷавоб ба Вазорати молия',
        'doc_type' => 'Содиротӣ',
        'sender' => 'Шарипов С.',
        'doc_date' => '2026-11-18',
        'status' => 'approved',
    ],
    [
        'id' => 7,
        'reg_number' => 'ДХ-2026/007',
        'title' => 'Фармоиши меҳнатӣ №12',
        'doc_type' => 'Дохилӣ',
        'sender' => 'Раҳимов А.',
        'doc_date' => '2026-11-17',
        'status' => 'in_progress',
    ],
    [
        'id' => 39,
        'reg_number' => 'ВР-2026/039',
        'title' => 'Шартномаи иҷора',
        'doc_type' => 'Воридотӣ',
        'sender' => 'Қодиров Ф.',
        'doc_date' => '2026-11-14',
        'status' => 'rejected',
    ],
    [
        'id' => 5,
        'reg_number' => 'ДХ-2026/005',
        'title' => 'Ҳисоботи моҳонаи октябр',
        'doc_type' => 'Дохилӣ',
        'sender' => 'Юсупова Н.',
        'doc_date' => '2026-11-10',
        'status' => 'done',
    ],
    [
        'id' => 99,
        'reg_number' => 'ВР-2026/099',
        'title' => '<img src=x onerror="alert(\'XSS\')"> Ҳуҷҷати санҷишӣ',
        'doc_type' => 'Дохилӣ',
        'sender' => 'Ҳусейнов У.',
        'doc_date' => '2026-11-22',
        'status' => 'registered',
    ],
];

$q = mb_strtolower(trim((string) ($_GET['q'] ?? '')));
$type = trim((string) ($_GET['type'] ?? ''));
$status = trim((string) ($_GET['status'] ?? ''));

if ($q !== '') {
    $documents = array_values(array_filter(
        $documents,
        static fn (array $d): bool =>
            str_contains(mb_strtolower($d['reg_number']), $q) ||
            str_contains(mb_strtolower($d['title']), $q) ||
            str_contains(mb_strtolower($d['sender']), $q)
    ));
}

if ($type !== '') {
    $typeMap = [
        'incoming' => 'Воридотӣ',
        'outgoing' => 'Содиротӣ',
        'internal' => 'Дохилӣ',
    ];
    $targetType = $typeMap[$type] ?? $type;
    $documents = array_values(array_filter(
        $documents,
        static fn (array $d): bool => $d['doc_type'] === $targetType
    ));
}

if ($status !== '') {
    $documents = array_values(array_filter(
        $documents,
        static fn (array $d): bool => $d['status'] === $status
    ));
}

echo json_encode($documents, JSON_UNESCAPED_UNICODE);