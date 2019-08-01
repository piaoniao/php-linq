<?php

namespace linq;


class Linq
{
    private $iterator;

    public function __construct($source)
    {
        if (!is_array($source)) {
            throw new \InvalidArgumentException();
        }
        $this->iterator = new \ArrayIterator($source);
    }

    public function toArrayIterator()
    {
        if ($this->iterator instanceof \ArrayIterator) {
            return $this->iterator;
        } else {
            return new \ArrayIterator($this->iterator);
        }
    }

    public static function from($source)
    {
        return new Linq($source);
    }

    public function where(\Closure $predicate)
    {
        $this->iterator = Utils::where($this->iterator, $predicate);
        return $this;
    }

    public function map(\Closure $closure)
    {
        $this->iterator = Utils::map($this->iterator, $closure);
        return $this;
    }

    public function join($array, \Closure $on, \Closure $result, $strategy = Constants::JOIN_NORMAL)
    {
        if (!is_array($array) && !$array instanceof \ArrayIterator) {
            throw new \InvalidArgumentException();
        }
        $this->iterator = Utils::join($this->iterator, $array, $on, $result, $strategy);
        return $this;
    }

    public function page($page, $pageSize)
    {
        $this->iterator = Utils::page($this->iterator, $page, $pageSize);
        return $this;
    }

    public function limit($count)
    {
        $this->iterator = Utils::limit($this->iterator, $count);
        return $this;
    }

    public function select()
    {
        $data = [];
        foreach ($this->iterator as $item) {
            $data[] = $item;
        }
        return $data;
    }

    public function find()
    {
        return $this->limit(1)->select();
    }

    public function all(\Closure $predicate)
    {
        foreach ($this->iterator as $index => $item) {
            if (!call_user_func($predicate, $item, $index)) {
                return false;
            }
        }
        return true;
    }

    public function any(\Closure $predicate)
    {
        foreach ($this->iterator as $index => $item) {
            if (call_user_func($predicate, $item, $index)) {
                return true;
            }
        }
        return false;
    }

    public function first($default = null)
    {
        foreach ($this->iterator as $item) {
            return $item;
        }

        return $default;
    }

    public function last($default = null)
    {
        $value = $default;
        foreach ($this->iterator as $item) {
            $value = $item;
        }

        return $value;
    }

    
}