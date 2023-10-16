<?php

$request = json_decode(file_get_contents('php://input'));

require '../mysql.php';
$pdo = getConnection();
$page = $request->page;
$config = parse_ini_file('../config.ini', true);
$limit = $config['page']['length'];
$offset = ($page - 1) * $limit;
$listQuery = 'SELECT s.id, COALESCE(i.ip, \'-\') as ip, COALESCE(i.description, \'-\') as description, s.name as server, o.name as owner
FROM `owners` AS o
JOIN `servers` AS s ON s.owner = o.id
LEFT JOIN `ip` AS i ON i.server_id = s.id
WHERE o.id = ?
LIMIT ' . $limit;
if ($offset) {
    $listQuery = $listQuery . "\nOFFSET " . $offset;
}
$st = m_pquery($pdo, $listQuery, [$request->owner,]);

$owners = $st->fetchAll(PDO::FETCH_ASSOC);
$countQuery = 'SELECT COUNT(*) as count
FROM `owners` AS o
JOIN `servers` AS s ON s.owner = o.id
LEFT JOIN `ip` AS i ON i.server_id = s.id
WHERE o.id = ?';
$count = m_pquery($pdo, $countQuery, [$request->owner,])->fetch(PDO::FETCH_ASSOC)['count'];
$pageCount = intval($count / $limit);
if ($count % $limit) {
    $pageCount++;
}
header('Content-Type: application/json');
$info = [
    'owners' => $owners,
    'count' => $pageCount,
    'page' => $page,
];
echo json_encode($info);
