<?php
$json = file_get_contents(__DIR__ . '/data.json');
$data = json_decode($json, true);

// デバッグ用に目印を入れる
$data['_debug_version'] = '2025-12-02-01';

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
