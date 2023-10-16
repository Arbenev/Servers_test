<?php

/**
 *
 * @return \PDO
 */
function getConnection()
{
    $config = parse_ini_file('config.ini', true);
    $dsn = 'mysql:dbname=' . $config['db']['database'] . ';host=' . $config['db']['host'];
    $user = $config['db']['user'];
    $password = $config['db']['password'];

    return new PDO($dsn, $user, $password);
}

/**
 *
 * @param PDO $mysql_connection
 * @param string $query
 * @param array $params
 * @return type
 */
function m_pquery(PDO $mysql_connection, string $query, array $params = [])
{
    $statement = $mysql_connection->prepare($query);
    foreach ($params as $num => $param) {
        $statement->bindParam($num + 1, $param);
    }
    $statement->execute();
    return $statement;
}
