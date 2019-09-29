<?php

namespace linq;


use ArrayIterator;
use Closure;
use Countable;
use InvalidArgumentException;
use Traversable;
use UnexpectedValueException;


/**
 * PHP Linq
 * PS: Only support indexed array.
 * Class Linq
 * @package linq
 */
class Linq
{
    private $iterator;

    public function __construct($source)
    {
        if (is_array($source)) {
            $this->iterator = new ArrayIterator($source);
        }
        if ($source instanceof Traversable) {
            $this->iterator = $source;
        }
        if ($this->iterator === null) {
            throw new InvalidArgumentException();
        }
    }

    /// Conversion

    public function toArrayIterator()
    {
        if ($this->iterator instanceof ArrayIterator) {
            return $this->iterator;
        } else {
            return new ArrayIterator($this->iterator);
        }
    }

    /// Generation

    public static function from($source)
    {
        return new Linq($source);
    }

    public static function range(int $start, int $count, int $step)
    {
        $generator = Utils::range($start, $count, $step);
        return new Linq($generator);
    }

    /// Projection and filtering

    public function map(Closure $selector)
    {
        $this->iterator = Utils::map($this->iterator, $selector);
        return $this;
    }

    public function selectMany(Closure $selector)
    {
        $this->iterator = Utils::selectMany($this->iterator, $selector);
        return $this;
    }

    public function where(Closure $predicate)
    {
        $this->iterator = Utils::where($this->iterator, $predicate);
        return $this;
    }

    /// Joining and grouping

    public function join($array, Closure $condition, Closure $resultSelector, $type = 'INNER')
    {
        if (!is_array($array) && !$array instanceof ArrayIterator) {
            throw new InvalidArgumentException();
        }
        $this->iterator = Utils::join($this->iterator, $array, $condition, $resultSelector, $type);
        return $this;
    }

    public function groupJoin($array, Closure $groupSelector, Closure $resultSelector)
    {
        $this->iterator = Utils::groupJoin($this->iterator, $array, $groupSelector, $resultSelector);
        return $this;
    }

    public function group(Closure $groupSelector, Closure $resultSelector)
    {
        $this->iterator = Utils::group($this->iterator, $groupSelector, $resultSelector);
        return $this;
    }

    /// Aggregation

    public function min(Closure $selector = null)
    {
        $min = PHP_INT_MAX;
        $found = false;
        foreach ($this as $index => $item) {
            if ($selector === null) {
                $min = min($min, $item);
            } else {
                $min = min($min, $selector($item, $index));
            }
            $found = true;
        }
        if (!$found) {
            throw new UnexpectedValueException();
        }
        return $min;
    }

    public function max(Closure $selector = null)
    {
        $max = PHP_INT_MIN;
        $found = false;
        foreach ($this as $index => $item) {
            if ($selector == null) {
                $max = max($max, $item);
            } else {
                $max = max($max, $selector($item, $index));
            }
            $found = true;
        }
        if (!$found) {
            throw new UnexpectedValueException();
        }
        return $max;
    }

    /// Output

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

    public function contains($value)
    {
        foreach ($this->iterator as $index => $item) {
            if ($value === $item) {
                return true;
            }
        }
        return false;
    }

    public function all(Closure $predicate)
    {
        foreach ($this->iterator as $index => $item) {
            if (!$predicate($item, $index)) {
                return false;
            }
        }
        return true;
    }

    public function any(Closure $predicate)
    {
        foreach ($this->iterator as $index => $item) {
            if ($predicate($item, $index)) {
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

    public function union($other, Closure $keySelector)
    {
        $this->iterator = Utils::union($this->iterator, $other, $keySelector);
        return $this;
    }

    public function intersect($other, Closure $keySelector)
    {
        $this->iterator = Utils::intersect($this->iterator, $other, $keySelector);
        return $this;
    }

    public function except($other, Closure $keySelector)
    {
        $this->iterator = Utils::except($this->iterator, $other, $keySelector);
        return $this;
    }

    public function prepend($item)
    {
        $this->iterator = Utils::prepend($this->iterator, $item);
        return $this;
    }

    public function append($item)
    {
        $this->iterator = Utils::append($this->iterator, $item);
        return $this;
    }

    public function distinct(Closure $keySelector)
    {
        $this->iterator = Utils::distinct($this->iterator, $keySelector);
        return $this;
    }

    public function concat($array)
    {
        if (!is_array($array) && !$array instanceof ArrayIterator) {
            throw new InvalidArgumentException();
        }
        $this->iterator = Utils::concat($this->iterator, $array);
        return $this;
    }

    public function sum(Closure $selector = null)
    {
        $sum = 0;
        foreach ($this as $index => $item) {
            if ($selector == null) {
                $sum += $item;
            } else {
                $sum += $selector($item, $index);
            }
        }
        return $sum;
    }

    public function average(Closure $selector = null)
    {
        $sum = $count = 0;
        foreach ($this->iterator as $index => $item) {
            if ($selector == null) {
                $sum += $item;
            } else {
                $sum += $selector($item, $index);
            }
            $count++;
        }
        if ($count === 0) {
            throw new UnexpectedValueException();
        }
        return $sum / $count;
    }

    public function aggregate(Closure $closure, $seed = null)
    {
        $result = $seed;
        foreach ($this->iterator as $index => $item) {
            $result = $closure($result, $item, $index);
        }
        return $result;
    }

    public function count(Closure $predicate = null): int
    {
        if ($this->iterator instanceof Countable && $predicate === null) {
            return count($this->iterator);
        }

        $count = 0;
        foreach ($this->iterator as $k => $v) {
            if ($predicate == null) {
                $count++;
            } else {
                if ($predicate($v, $k)) {
                    $count++;
                }
            }
        }
        return $count;
    }

}