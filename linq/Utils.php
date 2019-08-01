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

    public static function join($left, $right, $on, $strategy)
    {
        foreach ($left as $lk => $lv) {
            $isFound = false;
            foreach ($right as $rk => $rv) {
                if ($isFound && $strategy == Constants::JOIN_UNIQUE) {
                    break;
                }
                $isFound = call_user_func($on, $lv, $rv, $lk, $rk);
                if ($isFound) {
                    yield [$lv, $rv];
                }
            }
        }
    }

    public static function limit($iterator, $count)
    {
        $c = 0;
        foreach ($iterator as $item) {
            $c++;
            if ($c <= $count) {
                yield $item;
            } else {
                break;
            }
        }
    }
}