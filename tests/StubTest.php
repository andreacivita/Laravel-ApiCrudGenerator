<?php


use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class StubTest extends TestCase
{
    /**
     * @var Stub $stub
     */
    private $stub;

    public function setup() : void
    {
        $this->stub = new Stub(new Filesystem(), new Str());
    }

    /**
     * Test StubClass
     */
    public function testParseStub() : void
    {
        $content = $this->stub->parseStub("Controller", "Car");
        $this->assertIsString($content);
    }

    /**
     *
     */
    public function testFailParseStub() : void
    {
        $this->stub->parseStub("StubThatDoesNotExist", "Car");
        try {
            $this->stub->parseStub("StubThatDoesNotExist", "Car");
            $this->fail("Stub not found.");
        } catch (Exception $ex) {
            $this->assertEquals($ex->getMessage(), "Stub not found.");
        }
    }
}
