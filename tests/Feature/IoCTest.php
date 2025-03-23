<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\IoC\IoC;

class IoCTest extends TestCase
{
    public function testRegisterAndResolve()
    {
        IoC::Resolve('IoC.Register', 'test', function() {
            return new \stdClass();
        })->Execute();

        $instance = IoC::Resolve('test');
        $this->assertInstanceOf(\stdClass::class, $instance);
    }

    public function testScopes()
    {
        IoC::Resolve('Scopes.New', 'scope1')->Execute();
        IoC::Resolve('Scopes.Current', 'scope1')->Execute();

        $this->assertEquals('scope1', IoC::getCurrentScope());
    }
}