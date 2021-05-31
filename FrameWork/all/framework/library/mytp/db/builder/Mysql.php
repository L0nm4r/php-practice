<?php
namespace mytp\db\builder;

use mytp\db\Builder;
use mytp\db\Query;

class Mysql extends Builder
{
    public function select(Query $query)
    {
        $options = $query->getOptions();
        $field = $options['field'] ? ('`' . implode('`,`', $options['field']) . '`') : '*';
        $where = $options['where'] ? ' WHERE ' . $options['where'] : '';
        $order = $options['order'] ? ' ORDER BY ' . $options['order'] : '';
        $limit = $options['limit'] ? ' LIMIT ' . $options['limit'] : '';
        $table = '`' . $query->getTable() . '`';
        return 'SELECT ' . $field . ' FROM ' . $table . $where . $order . $limit;
    }

    public function insert(Query $query, $replace = false)
    {
        $options = $query->getOptions();
        $field = $this->buildField(array_keys($options['data']));
        $table = '`' . $query->getTable() . '`';
        $type = $replace ? 'REPLACE' : 'INSERT';
        return $type . ' INTO ' . $table . ' SET ' . $field;
    }

    protected function buildField(array $data = [])
    {
        return implode(',', array_map(function ($v) {
            return "`$v`=:$v";
        }, $data));
    }

    public function update(Query $query)
    {
        $options = $query->getOptions();
        $field = $this->buildField(array_keys($options['data']));
        $where = $options['where'] ? ' WHERE ' . $options['where'] : '';
        $order = $options['order'] ? ' ORDER BY ' . $options['order'] : '';
        $limit = $options['limit'] ? ' LIMIT ' . $options['limit'] : '';
        $table = '`' . $query->getTable() . '`';
        return 'UPDATE ' . $table . ' SET ' . $field . $where . $order . $limit;
    }

    public function delete(Query $query)
    {
        $options = $query->getOptions();
        $where = $options['where'] ? ' WHERE ' . $options['where'] : '';
        $order = $options['order'] ? ' ORDER BY ' . $options['order'] : '';
        $limit = $options['limit'] ? ' LIMIT ' . $options['limit'] : '';
        $table = '`' . $query->getTable() . '`';
        return 'DELETE FROM ' . $table . $where . $order . $limit;
    }
}
