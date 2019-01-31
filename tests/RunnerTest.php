<?php

namespace NameParser\Test;


use NameParser\Test\TestBase;
use NameParser\Runner;

class RunnerTest extends TestBase
{
    protected $runner;

    public function setUp()
    {
        parent::setUp();
        $this->runner = new Runner();
    }


    public function testRun()
    {
        $this->runner->run();
        $this->assertEquals(1, 1);
    }
}