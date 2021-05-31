<?php
namespace mytp\db;

use PDO;
use PDOException;
use Exception;
use mytp\Container;

abstract class Connection
{
    protected static $instance = [];

    protected $builder;

    protected $config = [
        'type' => 'mysql', 'hostname' => '', 'database' => '', 'username' => '',
        'password' => '', 'hostport' => '', 'charset' => 'utf8', 'prefix' => ''
    ];

    protected $linkID;  // 保存PDO数据库连接对象

    protected $PDOStatement;    // 保存prepare方法返回的对象

    protected $params = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // 数据库连接时传入的选项
    ];
    
    public static function instance(array $config = [])
    {
        $name = md5(serialize($config));
        if (!isset(self::$instance[$name])) {
            $class = '\\mytp\\db\\connector\\' . ucwords($config['type']);
            self::$instance[$name] = new $class($config);
        }
        return static::$instance[$name];
    }

    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }
        $class = '\\mytp\\db\\builder\\' . ucwords($this->config['type']);
        $this->builder = new $class();
    }
    
    public function getConfig($config = '')
    {
        return $config === '' ? $this->config : $this->config[$config];
    }

    /**
     * 连接数据库
     * Db::connect() 
     */
    public function connect()
    {
        if (is_null($this->linkID)) {
            $config = $this->config;
            try {
                $dsn = $this->parseDsn($config);
                $link = new PDO($dsn, $config['username'], $config['password'], $this->params);
            } catch (PDOException $e) {
                throw new Exception('连接数据库失败：' . $e->getMessage());
            }
            $this->linkID = $link;
        }
        return $this;
    }


    /**
     * 根据数据库返回配置生成$dsn参数
     * 因为数据库不同 dsn 格式也不同, 所以暂时不实现,到connector/Mysql.php再实现
     */
    abstract protected function parseDsn($config);

    public function query($sql, $bind = [])
    {
        $this->connect();
        $this->log($sql);
        try {
            $this->PDOStatement = $this->linkID->prepare($sql);
            $this->PDOStatement->execute($bind);
            return $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $err = implode('-', $this->PDOStatement->errorInfo());
            throw new Exception('数据库操作失败： '  . $err . ' [SQL：' . $sql . ']');
        }
    }

    public function execute($sql, $bind = [])
    {
        $this->connect();
        $this->log($sql);
        try {
            $this->PDOStatement = $this->linkID->prepare($sql);
            $this->PDOStatement->execute($bind);
            return $this->PDOStatement->rowCount();
        } catch (PDOException $err) {
            $err = implode('-', $this->PDOStatement->errorInfo());
            throw new Exception('数据库操作失败： '  . $err . ' [SQL：' . $sql . ']');
        }
    }

    public function getLastInsID()
    {
        return $this->linkID->lastInsertId();
    }

    public function select(Query $query)
    {
        $sql = $this->builder->select($query);
        return $this->query($sql, $query->getOptions('where_bind'));
    }

    public function insert(Query $query, $replace = false, $getLastInsID = false)
    {
        $sql = $this->builder->insert($query, $replace);
        $result = $this->execute($sql, $query->getOptions('data'));
        return $getLastInsID ? $this->getLastInsID() : $result;
    }

    public function update(Query $query)
    {
        $sql = $this->builder->update($query);
        $data = array_merge($query->getOptions('data'), $query->getOptions('where_bind'));
        return $this->execute($sql, $data);
    }

    public function delete(Query $query)
    {
        $sql = $this->builder->delete($query);
        return $this->execute($sql, $query->getOptions('where_bind'));
    }

    public function log($log, $type = 'sql')
    {
        Container::get('log')->record($log, $type);
    }

    public function startTrans()
    {
        $this->connect();
        return $this->linkID ? $this->linkID->beginTransaction() : false;
    }

    public function commit()
    {
        return $this->linkID ? $this->linkID->commit() : false;
    }

    public function rollBack()
    {
        return $this->linkID ? $this->linkID->rollBack() : false;
    }
}
