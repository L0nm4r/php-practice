<?php
namespace mytp\db;

use mytp\db\Connection;
use Exception;

class Query
{
    protected $connection;

    protected $prefix = '';

    protected $name;

    protected $options = [
        'field' => [], 'data' => [], 'where' => '', 'where_bind' => [],
        'order' => '', 'limit' => ''
    ];

    public function getOptions($name = '')
    {
        return $name === '' ? $this->options : $this->options[$name];
    }
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->prefix = $this->connection->getConfig('prefix');
    }

    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getTable($name = '')
    {
        return $this->prefix . ($name ?: $this->name);
    }

    public function field($field = [])
    {
        if (is_string($field)) {
            $field = array_map('trim', explode(',', $field));
        }
        $field = array_merge($this->options['field'], $field);
        $this->options['field'] = array_unique($field);
        return $this;
    }
    
    public function data(array $data = [])
    {
        $this->options['data'] = array_merge($this->options['data'], $data);
        return $this;
    }

    public function where($where = '', array $bind = [])
    {
        $this->options['where'] = $where;
        $this->options['where_bind'] = $bind;
        return $this;
    }
    
    public function limit($limit)
    {
        $this->options['limit'] = $limit;
        return $this;
    }

    public function order($order)
    {
        $this->options['order'] = $order;
        return $this;
    }

    public function select()
    {
        return $this->connection->select($this);
    }

    public function insert(array $data = [], $replace = false, $getLastInsID = false)
    {
        $this->data($data);
        return $this->connection->insert($this, $replace, $getLastInsID);
    }
    
    public function insertGetId(array $data, $replace = false)
    {
        return $this->insert($data, $replace, true);
    }

    public function update(array $data = [])
    {
        if (empty($this->options['where'])) {
            throw new Exception('update()缺少WHERE条件');
        }
        $this->data($data);
        foreach ($this->options['where_bind'] as $k => $v) {
            foreach ([$k, ':' . $k, ltrim($k, ':')] as $kk) {
                if (isset($this->options['data'][$kk])) {
                    throw new Exception('WHERE参数名 ' . $kk .' 已存在，请换一个参数名');
                }
            }
        }
        return $this->connection->update($this);
    }

    public function delete()
    {
        if (empty($this->options['where'])) {
            throw new Exception('delete()缺少WHERE条件');
        }
        return $this->connection->delete($this);
    }

    public function query($sql, $bind = [])
    {
        return $this->connection->query($sql, $bind);
    }

    public function execute($sql, $bind = [])
    {
        return $this->connection->execute($sql, $bind);
    }

    public function getLastInsID()
    {
        return $this->connection->getLastInsID();
    }

    public function startTrans()
    {
        return $this->connection->startTrans();
    }

    public function commit()
    {
        return $this->connection->commit();
    }
    
    public function rollback()
    {
        return $this->connection->rollback();
    }
}
