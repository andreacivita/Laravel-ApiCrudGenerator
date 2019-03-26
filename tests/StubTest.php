<?php


use AndreaCivita\ApiCrudGenerator\Core\Stub;
use PHPUnit\Framework\TestCase;

class StubTest extends TestCase
{

    /**
     * Test StubClass
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function testParseStub()
    {
        $stub = new Stub(new \Illuminate\Filesystem\Filesystem(),new \Illuminate\Support\Str());
        $content = $stub->parseStub("Controller","Car");
        $this->assertIsString($content);


    }
}