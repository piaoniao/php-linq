<?php

namespace linq;

class Linq
{
    private $data;
    private $defaultColumnName = '_it';
    private $alias;
    private $fields;
    private $wheres = [];

    public function __construct($array)
    {
        $this->data = $array;
    }

    public static function from($array)
    {
        return new Linq($array);
    }

    public function defaultColumnName($name)
    {
        $this->defaultColumnName = $name;
    }

    public function alias($alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function field($field)
    {
        if (is_string($field)) {
            $this->fields = explode(',', $field);
        } else if (is_array($field)) {
            $this->fields = $field;
        }
        return $this;
    }

    public function where($field, $op = null, $condition = null)
    {
        $this->wheres[] = [$field, $op, $condition];
        return $this;
    }

    public function select(\Closure $map = null)
    {
        $iterator = $this->toGenerator();
        $data = [];
        foreach ($iterator as $index => $item) {
            if ($map != null) {
                $item = $map($item, $index);
            }
            if (is_array($item) && $this->fields) {
                $item = $this->array_filter_field($item, $this->fields);
            }
            $data[] = $item;
        }
        return $data;
    }

    public function join($array, $alias, $on, $type = 'INNER')
    {
        return $this;
    }

    private function array_filter_field($obj, $fields)
    {
        if (is_string($fields)) {
            $fields = explode(',', $fields);
        }
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = $obj[$field];
        }
        return $data;
    }

    public function column($field)
    {
        $iterator = $this->toGenerator();
        $data = [];
        foreach ($iterator as $item) {
            $data[] = $item[$field];
        }
        return $data;
    }

    public function sum($field = null)
    {
        $iterator = $this->toGenerator();
        $sum = 0;
        foreach ($iterator as $item) {
            $value = ($field == null) ? $item : $item[$field];
            $sum += $value;
        }
        return $sum;
    }

    /**
     * @return \Generator
     */
    private function toGenerator()
    {
        foreach ($this->data as $index => $item) {
            if (!$this->checkWhere($item, $index)) {
                continue;
            }
            yield $item;
        }
    }

    /**
     * @param mixed $item
     * @param int $index
     * @return bool
     */
    private function checkWhere($item, $index)
    {
        foreach ($this->wheres as $where) {
            if ($where[0] instanceof \Closure) {
                $check = $where[0]($item, $index);
            } else if ($where[0] === $this->defaultColumnName) {
                $check = $this->checkWhereOp($item, $where[1], $where[2]);
            } else {
                $check = $this->checkWhereOp($item[$where[0]], $where[1], $where[2]);
            }
            if (!$check) {
                return false;
            }
        }
        return true;
    }

    private function checkWhereOp($field, $op, $value)
    {
        switch ($op) {
            case '=':
                if ($field != $value) {
                    return false;
                }
                break;
            case '>':
                if ($field <= $value) {
                    return false;
                }
                break;
            case '>=':
                if ($field < $value) {
                    return false;
                }
                break;
            case '<':
                if ($field >= $value) {
                    return false;
                }
                break;
            case '<=':
                if ($field > $value) {
                    return false;
                }
                break;
        }
        return true;
    }
}