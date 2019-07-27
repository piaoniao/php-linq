<?php


use linq\Linq;
use PHPUnit\Framework\TestCase;

class LinqTest extends TestCase
{
    // linq 1: Where - Simple 1
    public function testLinq1()
    {
        $numbers = [5, 4, 1, 3, 9, 8, 6, 7, 2, 0];

        $lowNums = Linq::from($numbers)
            ->where(null, '<', 5)
            ->select();

        $lowNums2 = Linq::from($numbers)
            ->where(function ($it) {
                return $it < 5;
            })
            ->select();

        $result = [4, 1, 3, 2, 0];
        $this->assertEquals(json_encode($result), json_encode($lowNums));
        $this->assertEquals(json_encode($result), json_encode($lowNums2));
    }

    // linq2: Where - Simple 2
    public function testLinq2()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
        ];

        $products1 = Linq::from($products)
            ->where('amount', '=', 0)
            ->select();

        $products2 = Linq::from($products)
            ->where(function ($it) {
                return $it['amount'] == 0;
            })
            ->select();

        $result = [
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
        ];
        $this->assertEquals(json_encode($result), json_encode($products1));
        $this->assertEquals(json_encode($result), json_encode($products2));
    }

    // linq3: Where - Simple 3
    public function testLinq3()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];

        $products1 = Linq::from($products)
            ->where('amount', '=', 0)
            ->where('price', '>', 80)
            ->select();

        $products2 = Linq::from($products)
            ->where(function ($it) {
                return $it['amount'] == 0 && $it['price'] > 80;
            })
            ->select();

        $result = [
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];
        $this->assertEquals(json_encode($result), json_encode($products1));
        $this->assertEquals(json_encode($result), json_encode($products2));
    }

    // linq4: Where - Drilldown
    public function testLinq4()
    {
        $products = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
            ['name' => 'P2', 'price' => 80, 'amount' => 0,],
            ['name' => 'P3', 'price' => 100, 'amount' => 0,],
        ];

        $products1 = Linq::from($products)
            ->where('name', '=', 'P1')
            ->select();

        $products2 = Linq::from($products)
            ->where(function ($it) {
                return $it['name'] == 'P1';
            })
            ->select();

        $result = [
            ['name' => 'P1', 'price' => 100, 'amount' => 10,],
        ];
        $this->assertEquals(json_encode($result), json_encode($products1));
        $this->assertEquals(json_encode($result), json_encode($products2));
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
}