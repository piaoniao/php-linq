<?php


use linq\Linq;
use PHPUnit\Framework\TestCase;

class LinqTest extends TestCase
{
    // linq 1: Where - Simple 1
    public function testLinq1()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];

        $test = Linq::from($numbers)
            ->where(function ($it) {
                return $it < 5;
            })
            ->select();

        $result = [4, 1, 3, 2, 0];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // linq2: Where - Simple 2
    public function testLinq2()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
        ];

        $test = Linq::from($products)
            ->where(function ($it) {
                return $it['amount'] == 0;
            })
            ->select();

        $result = [
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // linq3: Where - Simple 3
    public function testLinq3()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];

        $test = Linq::from($products)
            ->where(function ($it) {
                return $it['amount'] == 0 && $it['price'] > 80;
            })
            ->select();

        $result = [
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // linq4: Where - Drilldown
    public function testLinq4()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];

        $test = Linq::from($products)
            ->where(function ($it) {
                return $it['name'] == 'P1';
            })
            ->select();

        $result = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // linq5: Where - Indexed
    public function testLinq5()
    {
        $digits = ["zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];

        $test = Linq::from($digits)
            ->where(function ($it, $index) {
                return strlen($it) < $index;
            })
            ->select();

        $result = ["five", "six", "seven", "eight", "nine"];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // linq6: Select - Simple 1
    public function testLinq6()
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

    // linq7: Select - Simple 2
    public function testLinq7()
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

    // linq8: Select - Transformation
    public function testLinq8()
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

    // linq9: Select - Anonymous Types 1
    public function testLinq9()
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

    // linq10: Select - Anonymous Types 2
    public function testLinq10()
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

    // linq11: Select - Anonymous Types 3
    public function testLinq11()
    {
        // ignore
        $this->assertEquals(1, 1);
    }

    // linq12: Select - Indexed
    public function testLinq12()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];

        $test = Linq::from($numbers)
            ->map(function ($it, $index) {
                return [
                    'Num'     => $it,
                    'InPlace' => $it == $index ? 1 : 0,
                ];
            })
            ->select();

        $result = [
            ['Num' => 5, 'InPlace' => 0],
            ['Num' => 4, 'InPlace' => 0],
            ['Num' => 1, 'InPlace' => 0],
            ['Num' => 3, 'InPlace' => 1],
            ['Num' => 9, 'InPlace' => 0],
            ['Num' => 8, 'InPlace' => 0],
            ['Num' => 6, 'InPlace' => 1],
            ['Num' => 7, 'InPlace' => 1],
            ['Num' => 2, 'InPlace' => 0],
            ['Num' => 0, 'InPlace' => 0],
        ];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // linq13: Select - Filtered
    public function testLinq13()
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

    // linq14: Join (INNER LEFT RIGHT FULL)
    public function testJoin()
    {
        $numbersA = [0, 2, 4, 5, 6];
        $numbersB = [-1, 1, 3, 5, 7, 8];

        $test = Linq::from($numbersA)
            ->join($numbersB,
                function ($left, $right) {
                    return $left < $right;
                },
                function ($left, $right) {
                    return ['a' => $left, 'b' => $right];
                }, 'LEFT')
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

    // group 1
    public function testGroup1()
    {
        $source = [1, 3, 5, 7, 9];
        $test = Linq::from($source)
            ->group(
                function ($item) {
                    return $item % 4;
                },
                function ($item) {
                    return $item;
                }
            )
            ->select();

        $result = [1, 3];

        $this->assertEquals(json_encode($result), json_encode($test));
    }

    // group 2
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
                function ($item) {
                    return $item['name'];
                },
                function ($item) {
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

    // all
    public function testAll()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 10];

        $test = Linq::from($numbers)->all(function ($it) {
            return $it >= 1;
        });

        $this->assertEquals($test, true);
    }

    // first
    public function testFirst()
    {
        $numbers = [];

        $test = Linq::from($numbers)->first(1);

        $this->assertEquals($test, 1);
    }

    // range
    public function testRange()
    {
        $test = Linq::range(1, 5, 2)
            ->where(function ($it) {
                return $it < 6;
            })
            ->select();

        $result = [1, 3, 5];
        $this->assertEquals(json_encode($result), json_encode($test));
    }

}