<?php

namespace linq;


class Linq
{
    private $iterator;
    /**
     * @var \Closure
     */
    private $where;

    private $joins;

    public function __construct($source)
    {
        if (!is_array($source)) {
            throw new \InvalidArgumentException();
        }
        $this->iterator = new \ArrayIterator($source);
    }

    public static function from($source)
    {
        return new Linq($source);
    }

    public function where(\Closure $predicate)
    {
        $this->where = $predicate;
        return $this;
    }

    public function select(\Closure $map = null)
    {
        $data = [];
        foreach ($this->iterator as $index => $item) {
            if ($this->where) {
                if (!call_user_func($this->where, $item, $index)) {
                    continue;
                }
            }
            if ($this->joins) {
                // 暂时只能 1 层
                foreach ($this->joins['data'] as $joinItem) {
                    if (!call_user_func($this->joins['on'], $item, $joinItem)) {
                        continue;
                    }
                    if ($map != null) {
                        $newItem = $map($item, $joinItem);
                        $data[] = $newItem;
                    }
                }
            } else {
                if ($map != null) {
                    $item = $map($item, $index);
                }
                $data[] = $item;
            }
        }
        return $data;
    }

    public function join($array, $on)
    {
        $this->joins = [
            'type' => 'INNER',
            'data' => $array,
            'on'   => $on,
        ];
        return $this;
    }
}