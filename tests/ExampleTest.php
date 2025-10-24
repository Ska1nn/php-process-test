<?php

use PHPUnit\Framework\TestCase;
use App\Model\Example;

class ExampleTest extends TestCase
{
    public function testSumReturnsCorrectValue(): void
    {
        $example = new Example();
        $this->assertEquals(5, $example->sum(2, 3));
    }
}
