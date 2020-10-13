<?php


use linq\Linq;
use PHPUnit\Framework\TestCase;

class LinqTest extends TestCase
{
    // range
    public function testRange()
    {
        $test = Linq::range(1, 5, 2)
            ->where(function ($item) {
                return $item < 6;
            })
            ->select();

        $result = [1, 3, 5];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // selectMany
    public function testSelectMany()
    {
        $users = [
            [
                'name'    => 'A',
                'friends' => ['B', 'C', 'D'],
            ],
            [
                'name'    => 'B',
                'friends' => ['A', 'C'],
            ],
            [
                'name'    => 'C',
                'friends' => null,
            ],
            [
                'name'    => 'D',
                'friends' => [],
            ],
        ];
        $test = Linq::from($users)
            ->selectMany(function ($item, $index) {
                return $item['friends'];
            })
            ->distinct()
            ->select();
        $result = ['B', 'C', 'D', 'A'];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // where - Simple 1
    public function testWhere1()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];

        $test = Linq::from($numbers)
            ->where(function ($item) {
                return $item < 5;
            })
            ->select();

        $result = [4, 1, 3, 2, 0];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // where - Simple 2
    public function testWhere2()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
        ];

        $test = Linq::from($products)
            ->where(function ($item) {
                return $item['amount'] == 0;
            })
            ->select();

        $result = [
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // where - Simple 3
    public function testWhere3()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];

        $test = Linq::from($products)
            ->where(function ($item) {
                return $item['amount'] == 0 && $item['price'] > 80;
            })
            ->select();

        $result = [
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // where - Simple 4
    public function testWhere4()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];

        $test = Linq::from($products)
            ->where(function ($item) {
                return $item['name'] == 'P1';
            })
            ->select();

        $result = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // where - Indexed
    public function testWhere5()
    {
        $digits = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];

        $test = Linq::from($digits)
            ->where(function ($item, $index) {
                return strlen($item) < $index;
            })
            ->select();

        $result = ["five", "six", "seven", "eight", "nine"];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // join - INNER
    public function testJoin()
    {
        $numbersA = [0, 2, 4, 5, 6, 9];
        $numbersB = [-1, 1, 3, 5, 7, 8];

        $test = Linq::from($numbersA)
            ->join($numbersB,
                function ($left, $right, $leftIndex, $rightIndex) {
                    return $left < $right;
                },
                function ($left, $right, $leftIndex, $rightIndex) {
                    return ['a' => $left, 'b' => $right];
                })
            ->select();

        $result = [
            ['a' => 0, 'b' => 1], ['a' => 0, 'b' => 3], ['a' => 0, 'b' => 5], ['a' => 0, 'b' => 7], ['a' => 0, 'b' => 8],
            ['a' => 2, 'b' => 3], ['a' => 2, 'b' => 5], ['a' => 2, 'b' => 7], ['a' => 2, 'b' => 8],
            ['a' => 4, 'b' => 5], ['a' => 4, 'b' => 7], ['a' => 4, 'b' => 8],
            ['a' => 5, 'b' => 7], ['a' => 5, 'b' => 8],
            ['a' => 6, 'b' => 7], ['a' => 6, 'b' => 8],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // join - Left
    public function testLeftJoin()
    {
        $numbersA = [0, 2, 4, 5, 6, 9];
        $numbersB = [-1, 1, 3, 5, 7, 8];

        $test = Linq::from($numbersA)
            ->leftJoin($numbersB,
                function ($left, $right, $leftIndex, $rightIndex) {
                    return $left < $right;
                },
                function ($left, $right, $leftIndex, $rightIndex) {
                    return ['a' => $left, 'b' => $right];
                })
            ->select();

        $result = [
            ['a' => 0, 'b' => 1], ['a' => 0, 'b' => 3], ['a' => 0, 'b' => 5], ['a' => 0, 'b' => 7], ['a' => 0, 'b' => 8],
            ['a' => 2, 'b' => 3], ['a' => 2, 'b' => 5], ['a' => 2, 'b' => 7], ['a' => 2, 'b' => 8],
            ['a' => 4, 'b' => 5], ['a' => 4, 'b' => 7], ['a' => 4, 'b' => 8],
            ['a' => 5, 'b' => 7], ['a' => 5, 'b' => 8],
            ['a' => 6, 'b' => 7], ['a' => 6, 'b' => 8],
            ['a' => 9, 'b' => null],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // join - Right
    public function testRightJoin()
    {
        $numbersA = [0, 2, 4, 5, 6, 9];
        $numbersB = [-1, 1, 3, 5, 7, 8];

        $test = Linq::from($numbersA)
            ->rightJoin($numbersB,
                function ($left, $right, $leftIndex, $rightIndex) {
                    return $left < $right;
                },
                function ($left, $right, $leftIndex, $rightIndex) {
                    return ['a' => $left, 'b' => $right];
                })
            ->select();

        $result = [
            ['a' => 0, 'b' => 1], ['a' => 0, 'b' => 3], ['a' => 0, 'b' => 5], ['a' => 0, 'b' => 7], ['a' => 0, 'b' => 8],
            ['a' => 2, 'b' => 3], ['a' => 2, 'b' => 5], ['a' => 2, 'b' => 7], ['a' => 2, 'b' => 8],
            ['a' => 4, 'b' => 5], ['a' => 4, 'b' => 7], ['a' => 4, 'b' => 8],
            ['a' => 5, 'b' => 7], ['a' => 5, 'b' => 8],
            ['a' => 6, 'b' => 7], ['a' => 6, 'b' => 8],
            ['a' => null, 'b' => -1],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // join - FULL
    public function testFullJoin()
    {
        $numbersA = [0, 2, 4, 5, 6, 9];
        $numbersB = [-1, 1, 3, 5, 7, 8];

        $test = Linq::from($numbersA)
            ->fullJoin($numbersB,
                function ($left, $right, $leftIndex, $rightIndex) {
                    return $left < $right;
                },
                function ($left, $right, $leftIndex, $rightIndex) {
                    return ['a' => $left, 'b' => $right];
                })
            ->select();

        $result = [
            ['a' => 0, 'b' => 1], ['a' => 0, 'b' => 3], ['a' => 0, 'b' => 5], ['a' => 0, 'b' => 7], ['a' => 0, 'b' => 8],
            ['a' => 2, 'b' => 3], ['a' => 2, 'b' => 5], ['a' => 2, 'b' => 7], ['a' => 2, 'b' => 8],
            ['a' => 4, 'b' => 5], ['a' => 4, 'b' => 7], ['a' => 4, 'b' => 8],
            ['a' => 5, 'b' => 7], ['a' => 5, 'b' => 8],
            ['a' => 6, 'b' => 7], ['a' => 6, 'b' => 8],
            ['a' => 9, 'b' => null],
            ['a' => null, 'b' => -1],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // groupJoin
    public function testGroupJoin()
    {
        $numbersA = [0, 2, 4, 5, 6, 9];
        $numbersB = [-1, 1, 3, 5, 7, 8];

        $test = Linq::from($numbersA)
            ->groupJoin($numbersB,
                function ($left, $right, $leftIndex, $rightIndex) {
                    return $left % 2 == $right % 2 ? 1 : 0;
                },
                function ($left, $right, $group, $groupIndex) {
                    return ['group' => $group, 'a' => $left, 'b' => $right];
                })
            ->select();

        $result = [
            ['group' => 0, 'a' => 0, 'b' => -1],
            ['group' => 1, 'a' => 0, 'b' => 8],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // group - Simple 1
    public function testGroup1()
    {
        $source = [1, 3, 5, 7, 9];
        $test = Linq::from($source)
            ->group(
                function ($item, $index) {
                    return $item % 4;
                },
                function ($item, $group, $groupIndex) {
                    return $group;
                }
            )
            ->select();

        $result = [1, 3];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // group - Simple 2
    public function testGroup2()
    {
        $source = [
            ['id' => 1, 'name' => 'a'],
            ['id' => 2, 'name' => 'b'],
            ['id' => 3, 'name' => 'c'],
            ['id' => 1, 'name' => 'd2'],
            ['id' => 2, 'name' => 'd2'],
        ];
        $test = Linq::from($source)
            ->group(
                function ($item, $index) {
                    return $item['name'];
                },
                function ($item, $group, $groupIndex) {
                    return $item['name'];
                }
            )
            ->select();

        $result = ['a', 'b', 'c', 'd2'];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // page
    public function testPage()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];

        $test = Linq::from($numbers)
            ->page(2, 6)
            ->select();

        $this->assertEquals($test, [6, 7, 2, 0]);
    }

    // select - Simple 1
    public function testSelect1()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];

        $test = Linq::from($numbers)
            ->map(function ($it) {
                return $it + 1;
            })
            ->select();

        $result = [6, 5, 2, 4, 10, 9, 7, 8, 3, 1];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // select - Simple 2
    public function testSelect2()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
        ];

        $test = Linq::from($products)
            ->map(function ($it) {
                return ['name' => $it['name']];
            })
            ->select();

        $result = [
            ['name' => 'P1'],
            ['name' => 'P2'],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // select - Transformation
    public function testSelect3()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];
        $strings = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];

        $test = Linq::from($numbers)
            ->map(function ($it) use ($strings) {
                return $strings[$it];
            })
            ->select();

        $result = ['five', 'four', 'one', 'three', 'nine', 'eight', 'six', 'seven', 'two', 'zero',];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // select - Anonymous Types 1
    public function testSelect4()
    {
        $words = ["aPPLE", "BlUeBeRrY", "cHeRry"];

        $test = Linq::from($words)
            ->map(function ($it) {
                return [
                    'Upper' => strtoupper($it),
                    'Lower' => strtolower($it),
                ];
            })
            ->select();

        $result = [
            ['Upper' => 'APPLE', 'Lower' => 'apple'],
            ['Upper' => 'BLUEBERRY', 'Lower' => 'blueberry'],
            ['Upper' => 'CHERRY', 'Lower' => 'cherry'],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // select - Anonymous Types 2
    public function testSelect5()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];
        $strings = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];

        $test = Linq::from($numbers)
            ->map(function ($it) use ($strings) {
                return [
                    'Digit' => $strings[$it],
                    'Even'  => $it % 2 == 0 ? 1 : 0,
                ];
            })
            ->select();

        $result = [
            ['Digit' => 'five', 'Even' => 0],
            ['Digit' => 'four', 'Even' => 1],
            ['Digit' => 'one', 'Even' => 0],
            ['Digit' => 'three', 'Even' => 0],
            ['Digit' => 'nine', 'Even' => 0],
            ['Digit' => 'eight', 'Even' => 1],
            ['Digit' => 'six', 'Even' => 1],
            ['Digit' => 'seven', 'Even' => 0],
            ['Digit' => 'two', 'Even' => 1],
            ['Digit' => 'zero', 'Even' => 1],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // select - Filtered
    public function testSelect6()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];
        $digits = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];

        $test = Linq::from($numbers)
            ->where(function ($it) {
                return $it < 5;
            })
            ->map(function ($it) use ($digits) {
                return $digits[$it];
            })
            ->select();

        $result = ['four', 'one', 'three', 'two', 'zero',];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // find
    public function testFind()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];
        $digits = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];

        $test = Linq::from($numbers)
            ->where(function ($it) {
                return $it < 5;
            })
            ->map(function ($it) use ($digits) {
                return $digits[$it];
            })
            ->find();

        $result = 'four';
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // aggregate
    public function testAggregate()
    {
        $test = Linq::from([1, 2, 3, 4, 5])
            ->aggregate(function ($result, $item, $index) {
                return $result + $item;
            }, 0);

        $result = 15;

        $this->assertEquals($result, $test);
    }

    // count
    public function testCount()
    {
        $test = Linq::from([1, 2, 3, 4, 5])
            ->count(function ($item, $index) {
                return $item % 2 == 1;
            });

        $result = 3;

        $this->assertEquals($result, $test);
    }

    // average
    public function testAverage()
    {
        $test = Linq::from([1, 2, 3, 4, 5])->average();

        $result = 3;

        $this->assertEquals($result, $test);
    }

    // sum
    public function testSum()
    {
        $test = Linq::from([1, 2, 3, 4, 5])->sum();

        $result = 15;

        $this->assertEquals($result, $test);
    }

    public function testMin()
    {
        $test = Linq::from([6, 2, 3, 4, 5])->min();

        $result = 2;

        $this->assertEquals($result, $test);
    }

    public function testMax()
    {
        $test = Linq::from([1, 2, 7, 4, 5])->max();

        $result = 7;

        $this->assertEquals($result, $test);
    }

    // all
    public function testAll()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 10];

        $test = Linq::from($numbers)->all(function ($it) {
            return $it >= 1;
        });

        $this->assertEquals($test, true);

        $numbers = [5, 4, 0, 3, 9, 8, 6, 7, 2, 10];

        $test = Linq::from($numbers)->all(function ($it) {
            return $it >= 1;
        });

        $this->assertEquals($test, false);
    }

    // any
    public function testAny()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 10];

        $test = Linq::from($numbers)->any(function ($it) {
            return $it <= 10;
        });

        $this->assertEquals($test, true);

        $numbers = [5, 4, 0, 3, 9, 8, 6, 7, 2, 10];

        $test = Linq::from($numbers)->any(function ($it) {
            return $it > 10;
        });

        $this->assertEquals($test, false);
    }

    // contains
    public function testContains()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 10];

        $test = Linq::from($numbers)->contains(5);

        $this->assertEquals($test, true);
    }

    // append
    public function testAppend()
    {
        $numbers = [2, 3, 4];

        $test = Linq::from($numbers)->append(5)->select();

        $result = [2, 3, 4, 5];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // prepend
    public function testPrepend()
    {
        $numbers = [2, 3, 4];

        $test = Linq::from($numbers)->prepend(1)->select();

        $result = [1, 2, 3, 4];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // insert
    public function testInsert()
    {
        $numbers = [1, 3, 4];

        $test = Linq::from($numbers)->insert(1, 2)->select();

        $result = [1, 2, 3, 4];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // insertAll
    public function testInsertAll()
    {
        $numbers = [1, 3, 4];

        $test = Linq::from($numbers)->insertAll(2, [7, 8, 9])->select();

        $result = [1, 3, 7, 8, 9, 4];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // concat
    public function testConcat()
    {
        $numbersA = [2, 3, 4];
        $numbersB = [5, 6];

        $test = Linq::from($numbersA)->concat($numbersB)->select();

        $result = [2, 3, 4, 5, 6];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // distinct - Simple 1
    public function testDistinct1()
    {
        $numbers = [2, 3, 4, 4, 2];

        $test = Linq::from($numbers)->distinct()->select();

        $result = [2, 3, 4];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // distinct - Simple 2
    public function testDistinct2()
    {
        $users = [
            ['name' => 'a', 'gender' => 0],
            ['name' => 'b', 'gender' => 1],
            ['name' => 'c', 'gender' => 0],
        ];

        $test = Linq::from($users)
            ->distinct(function ($item, $index) {
                return $item['gender'];
            })
            ->select();

        $result = [
            ['name' => 'a', 'gender' => 0],
            ['name' => 'b', 'gender' => 1],
        ];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // except
    public function testExcept()
    {
        $numbersA = [2, 3, 4];
        $numbersB = [3, 6];

        $test = Linq::from($numbersA)
            ->except($numbersB, function ($item, $index) {
                return $item;
            })
            ->select();

        $result = [2, 4];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // intersect
    public function testIntersect()
    {
        $numbersA = [2, 3, 4];
        $numbersB = [3, 6];

        $test = Linq::from($numbersA)
            ->intersect($numbersB, function ($item, $index) {
                return $item;
            })
            ->select();

        $result = [3];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // union
    public function testUnion()
    {
        $numbersA = [2, 3, 4];
        $numbersB = [3, 6];

        $test = Linq::from($numbersA)
            ->union($numbersB, function ($item, $index) {
                return $item;
            })
            ->select();

        $result = [2, 3, 4, 6];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // first
    public function testFirst()
    {
        $numbers = [];

        $test = Linq::from($numbers)->first(1);

        $this->assertEquals($test, 1);
    }


}