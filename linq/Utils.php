<?php


namespace linq;


use ArrayAccess;
use Closure;
use InvalidArgumentException;

class Utils
{
    public static function range(int $start, int $count, int $step = 1)
    {
        if ($count <= 0) {
            throw new InvalidArgumentException('The $count must be greater than 0.');
        }
        yield $start;
        $count--;
        $value = $start;
        while ($count > 0) {
            yield $value += $step;
            $count--;
        }
    }

    public static function map($iterator, Closure $selector)
    {
        foreach ($iterator as $index => $item) {
            yield $selector($item, $index);
        }
    }

    public static function selectMany($iterator, Closure $selector)
    {
        foreach ($iterator as $index => $item) {
            $collection = $selector($item, $index);
            if ($collection) {
                if (is_array($collection) || $collection instanceof ArrayAccess) {
                    foreach ($collection as $subItem) {
                        yield $subItem;
                    }
                } else {
                    throw new InvalidArgumentException('The return value of $selector must be an array object.');
                }
            }
        }
    }

    public static function where($iterator, Closure $predicate)
    {
        foreach ($iterator as $index => $item) {
            if ($predicate($item, $index)) {
                yield $item;
            }
        }
    }

    public static function join($left, $right, Closure $predicate, Closure $resultSelector, string $type = 'INNER')
    {
        $rightOns = [];
        foreach ($left as $lk => $lv) {
            $leftOn = false;
            foreach ($right as $rk => $rv) {
                $on = $predicate($lv, $rv, $lk, $rk);
                if ($on) {
                    $leftOn = true;
                    $rightOns[$rk] = 1;
                    yield $resultSelector($lv, $rv, $lk, $rk);
                }
            }
            if (($type === 'LEFT' || $type === 'FULL') && !$leftOn) {
                yield $resultSelector($lv, null, $lk, null);
            }
        }
        if ($type === 'RIGHT' || $type === 'FULL') {
            foreach ($right as $rk => $rv) {
                if (!isset($rightOns[$rk])) {
                    yield $resultSelector(null, $rv, null, $rk);
                }
            }
        }
    }

    public static function groupJoin($left, $right, $groupSelector, $resultSelector)
    {
        $groups = [];
        $groupIndex = 0;
        foreach ($left as $lk => $lv) {
            foreach ($right as $rk => $rv) {
                $group = $groupSelector($lv, $rv, $lk, $rk);
                if (!is_string($group) && !is_int($group)) {
                    throw new InvalidArgumentException('The return value of $groupSelector must be a string or integer.');
                }
                if (!isset($groups[$group])) {
                    $groups[$group] = 1;
                    yield $resultSelector($lv, $rv, $group, $groupIndex);
                    $groupIndex++;
                }
            }
        }
    }

    public static function group($iterator, Closure $groupSelector, Closure $resultSelector)
    {
        $groups = [];
        $groupIndex = 0;
        foreach ($iterator as $index => $item) {
            $group = $groupSelector($item, $index);
            if (!is_string($group) && !is_int($group)) {
                throw new InvalidArgumentException('The return value of $groupSelector must be a string or integer.');
            }
            if (!isset($groups[$group])) {
                $groups[$group] = 1;
                yield $resultSelector($item, $group, $groupIndex);
                $groupIndex++;
            }
        }
    }

    public static function page($iterator, $page, $pageSize)
    {
        if ($pageSize <= 0) {
            throw new InvalidArgumentException();
        }
        if ($page <= 0) {
            $page = 1;
        }
        $start = ($page - 1) * $pageSize;
        $end = $page * $pageSize - 1;
        $crr = 0;
        foreach ($iterator as $item) {
            if ($crr > $end) {
                break;
            }
            if ($start <= $crr && $crr <= $end) {
                yield $item;
            }
            $crr++;
        }
    }

    public static function append($iterator, $item)
    {
        yield from $iterator;
        yield $item;
    }

    public static function concat($iterator, $array)
    {
        yield from $iterator;
        yield from $array;
    }

    public static function distinct($iterator, Closure $keySelector = null)
    {
        $set = [];
        foreach ($iterator as $index => $item) {
            if ($keySelector === null) {
                if (!is_string($item) && !is_int($item)) {
                    throw new InvalidArgumentException('The $item must be a string or integer value when the $keySelector is null.');
                }
                if (isset($set[$item])) {
                    continue;
                }
                $set[$item] = true;
            } else {
                $key = $keySelector($item, $index);
                if (!is_string($key) && !is_int($key)) {
                    throw new InvalidArgumentException('The return value of $keySelector must be a string or integer.');
                }
                if (isset($set[$key])) {
                    continue;
                }
                $set[$key] = true;
            }
            yield $item;
        }
    }

    public static function except($iterator, $other, Closure $keySelector)
    {
        $set = [];
        foreach ($other as $index => $item) {
            $key = $keySelector($item, $index);
            $set[$key] = true;
        }
        foreach ($iterator as $index => $item) {
            $key = $keySelector($item, $index);
            if (isset($set[$key])) {
                continue;
            }
            yield $item;
        }
    }

    public static function intersect($iterator, $other, Closure $keySelector)
    {
        $set = [];
        foreach ($iterator as $index => $item) {
            $key = $keySelector($item, $index);
            $set[$key] = true;
        }
        foreach ($other as $index => $item) {
            $key = $keySelector($item, $index);
            if (!isset($set[$key])) {
                continue;
            }
            unset($set[$key]);
            yield $item;
        }
    }

    public static function prepend($iterator, $item)
    {
        yield $item;
        yield from $iterator;
    }

    public static function union($iterator, $other, Closure $keySelector)
    {
        $set = [];
        foreach ($iterator as $index => $item) {
            $key = $keySelector($item, $index);
            if (isset($set[$key])) {
                continue;
            }
            $set[$key] = true;
            yield $item;
        }
        foreach ($other as $index => $item) {
            $key = $keySelector($item, $index);
            if (isset($set[$key])) {
                continue;
            }
            $set[$key] = true;
            yield $item;
        }
    }

    public static function skip($iterator, $count)
    {
        foreach ($iterator as $item) {
            if ($count > 0) {
                $count--;
                continue;
            }
            yield $item;
        }
    }

    public static function skipWhile($iterator, Closure $predicate)
    {
        $yielding = false;
        foreach ($iterator as $index => $item) {
            if (!$yielding && $predicate($item, $index)) {
                $yielding = true;
            }
            if ($yielding) {
                yield $item;
            }
        }
    }

    public static function take($iterator, $count)
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

    public static function takeWhile($iterator, $predicate)
    {
        foreach ($iterator as $index => $item) {
            if (!$predicate($item, $index)) {
                break;
            }
            yield $item;
        }
    }
}