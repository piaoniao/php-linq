<?php


namespace linq;


class Utils
{
    /**
     * @param $iterator
     * @param \Closure $predicate
     * @return \Generator
     */
    public static function where($iterator, $predicate)
    {
        foreach ($iterator as $index => $item) {
            if (!call_user_func($predicate, $item, $index)) {
                continue;
            }
            yield $item;
        }
    }

    /**
     * @param $iterator
     * @param \Closure $closure
     * @return \Generator
     */
    public static function map($iterator, $closure)
    {
        foreach ($iterator as $index => $item) {
            yield call_user_func($closure, $item, $index);
        }
    }
}