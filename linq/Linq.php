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

    public function join($array, $on, $type = 'INNER')
    {
        if (!is_array($array) && !$array instanceof \ArrayIterator) {
            throw new \InvalidArgumentException();
        }
        $this->iterator = Utils::join($this->iterator, $array, $on, $type);
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
        foreach ($this->iterator as $index => $item) {
            $data[] = $item;
        }
        return $data;
    }
}