<?php
namespace mytp\db\connector;

use mytp\db\Connection;

class Mysql extends Connection
{
    protected function parseDsn($config)
    {
        $dsn = "mysql:host={$config['hostname']};port={$config['hostport']};";
        $dsn .= "dbname={$config['database']};charset={$config['charset']}";
        return $dsn;
    }
}
