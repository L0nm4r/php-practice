<?php
namespace app\common\library;

/**
 * 树形结构的处理
 */
class Tree
{
    protected $idName = 'id';
    protected $pidName = 'pid';
    protected $subName = 'sub'; // 子元素数组key
    protected $data = [];
    protected $levelName = 'level';

    public function __construct(array $data = [])
    {
        $this->data($data);
    }

    public function data(array $data = [])
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getTree()
    {
        return $this->tree($this->data);
    }

    /**
     * 生成一个多为嵌套的数组,在每一项数组中,使用sub元素表示子元素数组
     */
    public function tree($data, $pid = 0)
    {
        $result = [];
        foreach ($data as $v) {
            if ($v[$this->pidName] === $pid) {
                $sub = $this->tree($data, $v[$this->idName]);
                $v[$this->subName] = $sub;
                $result[] = $v;
            }
        }
        return $result;
    }

    public function treeList($data, $pid = 0, $level = 0, &$tree = [])
    {
        foreach ($data as $v) {
            if ($v[$this->pidName] == $pid) {
                $v[$this->levelName] = $level;
                $tree[] = $v;
                $this->treeList($data, $v['id'], $level + 1, $tree);
            }
        }
        return $tree;
    }

    public function getTreeList()
    {
        return $this->treeList($this->data);
    }

    public function getTreeListCheckLeaf($name = 'isLeaf')
    {
        $data = $this->getTreeList();
        foreach ($data as $k => $v) {
            foreach ($data as $vv) {
                $data[$k][$name] = true;
                if ($v[$this->idName] === $vv[$this->pidName]) {
                    $data[$k][$name] = false;
                    break;
                }
            }
        }
        return $data;
    }
}
