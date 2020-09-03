<?php

namespace linq;


use ArrayAccess;
use ArrayIterator;
use Closure;
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

    /// Generation

    /**
     * @param array|Traversable $source
     * @return Linq
     */
    public static function from($source)
    {
        return new Linq($source);
    }

    /**
     * @param int $start
     * @param int $count
     * @param int $step
     * @return Linq
     */
    public static function range(int $start, int $count, int $step)
    {
        $generator = Utils::range($start, $count, $step);
        return new Linq($generator);
    }

    /// Projection and filtering

    /**
     * @param Closure $selector $selector($item, $index) : mixed
     * @return $this
     */
    public function map(Closure $selector)
    {
        $this->iterator = Utils::map($this->iterator, $selector);
        return $this;
    }

    /**
     * @param Closure $selector $selector($item, $index) : array
     * @return $this
     */
    public function selectMany(Closure $selector)
    {
        $this->iterator = Utils::selectMany($this->iterator, $selector);
        return $this;
    }

    /**
     * @param Closure $predicate $predicate($item, $index) : bool
     * @return $this
     */
    public function where(Closure $predicate)
    {
        $this->iterator = Utils::where($this->iterator, $predicate);
        return $this;
    }

    /// Joining and grouping

    /**
     * @param array|arrayAccess $array
     * @param Closure $predicate $predicate($left, $right, $leftIndex, $rightIndex) : bool
     * @param Closure $resultSelector $resultSelector($left, $right, $leftIndex, $rightIndex) : mixed
     * @param string $type 'INNER' or 'LEFT' or 'RIGHT' or 'FULL'
     * @return $this
     */
    public function join($array, Closure $predicate, Closure $resultSelector, $type = 'INNER')
    {
        $this->iterator = Utils::join($this->iterator, $array, $predicate, $resultSelector, $type);
        return $this;
    }

    /**
     * @param array|arrayAccess $array
     * @param Closure $predicate $predicate($left, $right, $leftIndex, $rightIndex) : bool
     * @param Closure $resultSelector $resultSelector($left, $right, $leftIndex, $rightIndex) : mixed
     * @return $this
     */
    public function leftJoin($array, Closure $predicate, Closure $resultSelector)
    {
        $this->iterator = Utils::join($this->iterator, $array, $predicate, $resultSelector, 'LEFT');
        return $this;
    }

    /**
     * @param array|arrayAccess $array
     * @param Closure $predicate $predicate($left, $right, $leftIndex, $rightIndex) : bool
     * @param Closure $resultSelector $resultSelector($left, $right, $leftIndex, $rightIndex) : mixed
     * @return $this
     */
    public function rightJoin($array, Closure $predicate, Closure $resultSelector)
    {
        $this->iterator = Utils::join($this->iterator, $array, $predicate, $resultSelector, 'RIGHT');
        return $this;
    }

    /**
     * @param array|arrayAccess $array
     * @param Closure $predicate $predicate($left, $right, $leftIndex, $rightIndex) : bool
     * @param Closure $resultSelector $resultSelector($left, $right, $leftIndex, $rightIndex) : mixed
     * @return $this
     */
    public function fullJoin($array, Closure $predicate, Closure $resultSelector)
    {
        $this->iterator = Utils::join($this->iterator, $array, $predicate, $resultSelector, 'FULL');
        return $this;
    }

    /**
     * @param array|arrayAccess $array
     * @param Closure $groupSelector $groupSelector($left, $right, $leftIndex, $rightIndex) : string|int
     * @param Closure $resultSelector $resultSelector($left, $right, $group, $groupIndex) : mixed
     * @return $this
     */
    public function groupJoin($array, Closure $groupSelector, Closure $resultSelector)
    {
        $this->iterator = Utils::groupJoin($this->iterator, $array, $groupSelector, $resultSelector);
        return $this;
    }

    /**
     * @param Closure $groupSelector $groupSelector($item, $index) : string|int
     * @param Closure $resultSelector $resultSelector($item, $group, $groupIndex) : mixed
     * @return $this
     */
    public function group(Closure $groupSelector, Closure $resultSelector)
    {
        $this->iterator = Utils::group($this->iterator, $groupSelector, $resultSelector);
        return $this;
    }

    /// Select

    /**
     * @param int $page
     * @param int $pageSize
     * @return $this
     */
    public function page($page, $pageSize)
    {
        $this->iterator = Utils::page($this->iterator, $page, $pageSize);
        return $this;
    }

    /**
     * @return array
     */
    public function select()
    {
        $data = [];
        foreach ($this->iterator as $item) {
            $data[] = $item;
        }
        return $data;
    }

    /**
     * @return mixed|null
     */
    public function find()
    {
        foreach ($this->iterator as $item) {
            return $item;
        }
        return null;
    }

    /// Aggregation

    /**
     * @param Closure $selector $selector($result, $item, $index) : mixed
     * @param mixed $seed
     * @return mixed|null
     */
    public function aggregate(Closure $selector, $seed = null)
    {
        return Utils::aggregate($this->iterator, $selector, $seed);
    }

    /**
     * @param Closure|null $predicate $predicate($item, $index) : bool
     * @return int
     */
    public function count(Closure $predicate = null): int
    {
        return Utils::count($this->iterator, $predicate);
    }

    /**
     * @param Closure|null $selector $selector($item, $index) : mixed
     * @return float|int
     */
    public function average(Closure $selector = null)
    {
        return Utils::average($this->iterator, $selector);
    }

    /**
     * @param Closure|null $selector $selector($item, $index) : int
     * @return int|mixed
     */
    public function sum(Closure $selector = null)
    {
        return Utils::sum($this->iterator, $selector);
    }

    /**
     * @param Closure|null $selector $selector($item, $index) : int
     * @return int|mixed
     */
    public function min(Closure $selector = null)
    {
        return Utils::min($this->iterator, $selector);
    }

    /**
     * @param Closure|null $selector $selector($item, $index) : int
     * @return int|mixed
     */
    public function max(Closure $selector = null)
    {
        return Utils::max($this->iterator, $selector);
    }

    /// Set

    /**
     * @param Closure $predicate $predicate($item, $index) : bool
     * @return bool
     */
    public function all(Closure $predicate)
    {
        return $this->all($predicate);
    }

    /**
     * @param Closure $predicate $predicate($item, $index) : bool
     * @return bool
     */
    public function any(Closure $predicate)
    {
        return Utils::any($this->iterator, $predicate);
    }

    public function append($item)
    {
        $this->iterator = Utils::append($this->iterator, $item);
        return $this;
    }

    public function concat($array)
    {
        if (!is_array($array) && !$array instanceof ArrayAccess) {
            throw new InvalidArgumentException();
        }
        $this->iterator = Utils::concat($this->iterator, $array);
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

    public function distinct(Closure $keySelector = null)
    {
        $this->iterator = Utils::distinct($this->iterator, $keySelector);
        return $this;
    }

    public function except($other, Closure $keySelector)
    {
        $this->iterator = Utils::except($this->iterator, $other, $keySelector);
        return $this;
    }

    public function intersect($other, Closure $keySelector)
    {
        $this->iterator = Utils::intersect($this->iterator, $other, $keySelector);
        return $this;
    }

    public function prepend($item)
    {
        $this->iterator = Utils::prepend($this->iterator, $item);
        return $this;
    }

    public function union($other, Closure $keySelector)
    {
        $this->iterator = Utils::union($this->iterator, $other, $keySelector);
        return $this;
    }

    /// Pagination

    public function elementAt($key, $default = null)
    {
        if ($this->iterator instanceof ArrayAccess) {
            if ($this->iterator->offsetExists($key)) {
                return $this->iterator->offsetGet($key);
            } else {
                return $default;
            }
        }

        foreach ($this->iterator as $k => $v) {
            if ($k === $key) {
                return $v;
            }
        }
        return $default;
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

    public function single(Closure $predicate = null, $default = null)
    {
        $found = false;
        $value = null;
        foreach ($this->iterator as $index => $item) {
            if ($predicate === null) {
                $found = true;
                $value = $item;
                break;
            } else {
                if ($predicate($item, $index)) {
                    $found = true;
                    $value = $item;
                    break;
                }
            }
        }
        if (!$found) {
            $value = $default;
        }
        return $value;
    }

    public function indexOf($value)
    {
        foreach ($this->iterator as $index => $item) {
            if ($item === $value) {
                return $index;
            }
        }
        return false;
    }

    public function lastIndexOf($value)
    {
        $key = false;
        foreach ($this->iterator as $index => $item) {
            if ($item === $value) {
                $key = $index;
            }
        }
        return $key;
    }

    public function findIndex($predicate)
    {
        foreach ($this->iterator as $index => $item) {
            if ($predicate($item, $index)) {
                return $index;
            }
        }
        return false;
    }

    public function findLastIndex($predicate)
    {
        $key = false;
        foreach ($this->iterator as $index => $item) {
            if ($predicate($item, $index)) {
                $key = $index;
            }
        }
        return $key;
    }

    public function skip(int $count)
    {
        $this->iterator = Utils::skip($this->iterator, $count);
        return $this;
    }

    public function skipWhile(Closure $predicate)
    {
        $this->iterator = Utils::skipWhile($this->iterator, $predicate);
        return $this;
    }

    public function take($count)
    {
        $this->iterator = Utils::take($this->iterator, $count);
        return $this;
    }

    public function takeWhile($predicate)
    {
        $this->iterator = Utils::takeWhile($this->iterator, $predicate);
        return $this;
    }
}