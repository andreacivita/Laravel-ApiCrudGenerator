<?php

namespace Unit;

use AndreaCivita\ApiCrudGenerator\Core\Stub;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class StubTest extends TestCase
{
    /**
     * @var Stub $stub
     */
    private $stub;

    private $names;

    public function setup(): void
    {
        $this->stub = new Stub(new Filesystem(), new Str());
        $this->names = [
            'Controller',
            'Factory',
            'Model',
            'Passport-Routes',
            'Request',
            'Resource',
            'Routes',
            'Test',
        ];
    }

    public function testGetStub()
    {
        foreach ($this->names as $name) {
            echo "Fetching $name stub... \n";
            $this->assertIsString($this->stub->getStub($name));
            echo $name . " ✅ \n";
        }

        $this->expectException(FileNotFoundException::class);
        $this->stub->getStub('StubThatDoesNotExist');
    }

    /**
     * Test StubClass
     */
    public function testParseStub(): void
    {
        foreach ($this->names as $name) {
            $this->assertIsString($this->stub->parseStub($name, 'Car', ['table' => 'cars']));
        }
    }
}
