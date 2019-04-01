<?php


use AndreaCivita\ApiCrudGenerator\Core\Stub;
use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class StubTest extends TestCase
{

    /**
     * Test StubClass
     */
    public function testParseStub()
    {
        $stub = new Stub(new \Illuminate\Filesystem\Filesystem(), new \Illuminate\Support\Str());
        $content = $stub->parseStub("Controller", "Car");
        $this->assertIsString($content);
    }

    /**
     *
     */
    public function testFailParseStub()
    {
        $stub = new Stub(new \Illuminate\Filesystem\Filesystem(), new \Illuminate\Support\Str());
        $stub->parseStub("StubThatDoesNotExist", "Car");
        try {
            $stub->parseStub("StubThatDoesNotExist", "Car");
            $this->fail("Stub not found.");
        }
        catch (Exception $ex) {
            $this->assertEquals($ex->getMessage(), "Stub not found.");
        }
    }
}