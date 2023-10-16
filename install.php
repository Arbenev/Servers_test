<?php

$config = parse_ini_file('config.ini', true);
define('NUMBER_OF_OWNERS', $config['install']['number_of_owners']);
define('NUMBER_OF_SERVERS', $config['install']['number_of_servers']);

require './mysql.php';
$createTablesSql = [
    'CREATE TABLE `owners` (`id` int(11) NOT NULL, `name` varchar(32) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
    'ALTER TABLE `owners` ADD PRIMARY KEY (`id`);',
    'ALTER TABLE `owners` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;',
    'CREATE TABLE `servers` (`id` int(11) NOT NULL, `name` varchar(32) NOT NULL, `owner` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
    'ALTER TABLE `servers` ADD PRIMARY KEY (`id`), ADD KEY `owner` (`owner`);',
    'ALTER TABLE `servers` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;',
    'ALTER TABLE `servers` ADD CONSTRAINT `FK_servers_owner` FOREIGN KEY (`owner`) REFERENCES `owners` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'CREATE TABLE `ip` (`id` int(11) NOT NULL, `ip` varchar(32) NOT NULL, `description` text NOT NULL, `server_id` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
    'ALTER TABLE `ip` ADD PRIMARY KEY (`id`), ADD KEY `server_id` (`server_id`);',
    'ALTER TABLE `ip` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;',
    'ALTER TABLE `ip` ADD CONSTRAINT `FK_ip_servers` FOREIGN KEY (`server_id`) REFERENCES `servers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;',
];

$pdo = getConnection();
foreach ($createTablesSql as $sql) {
    $pdo->query($sql);
}
for ($i = 1; $i <= NUMBER_OF_OWNERS; $i++) {
    $query = 'INSERT INTO `owners` (`name`) VALUES (\'Owner ' . $i . '\')';
    $pdo->query($query);
}
for ($i = 1; $i <= NUMBER_OF_SERVERS; $i++) {
    $query = 'INSERT INTO `servers` (`name`, `owner`) VALUES (\'Server ' . $i . '\', ' . random_int(1, NUMBER_OF_OWNERS) . ')';
    $pdo->query($query);
}
$servers = m_pquery($pdo, 'SELECT * FROM `servers` ORDER BY `id`');
foreach ($servers->fetchAll(PDO::FETCH_ASSOC) as $server) {
    if (random_int(1, 10) > 5) {
        $ip = implode('.', [
            random_int(1, 255),
            random_int(0, 255),
            random_int(0, 255),
            random_int(0, 255),
        ]);
        $description = 'Description ' . $ip . ' of server ' . $server['name'];
        $server_id = $server['id'];
        $query = 'INSERT INTO `ip` (`ip`, `description`, `server_id`) VALUES (\'' . implode('\',\'', [$ip, $description, $server_id]) . '\')';
        $pdo->query($query);
    }
}
