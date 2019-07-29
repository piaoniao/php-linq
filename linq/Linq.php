<?php

namespace linq;

class Linq
{
    private $data;
    /**
     * @var \Closure
     */
    private $where;

    public function __construct($array)
    {
        $this->data = $array;
    }

    public static function from($array)
    {
        return new Linq($array);
    }

    public function where(\Closure $condition)
    {
        $this->where = $condition;
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
            $data[] = $item;
        }
        return $data;
    }

    public function join($array, $on)
    {

    }

    /**
     * @return \Generator
     */
    private function toGenerator()
    {
        foreach ($this->data as $index => $item) {
            if ($this->where) {
                if (!call_user_func($this->where, $item, $index)) {
                    continue;
                }
            }
            yield $item;
        }
    }
}