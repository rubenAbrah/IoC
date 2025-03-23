<?php

use PHPUnit\Framework\TestCase;
use App\IoC\IoC;

class IoCTest extends TestCase
{
    protected function tearDown(): void
    {
        IoC::Resolve('Scopes.Current', 'default');
    }

    public function testRegisterAndResolveDependency()
    {
        IoC::Resolve('IoC.Register', 'test', function () {
            return new stdClass();
        });

        $instance = IoC::Resolve('test');
        $this->assertInstanceOf(stdClass::class, $instance);
    }

    public function testSingletonDependency()
    {
        IoC::Resolve('IoC.Register', 'singleton', function () {
            return new stdClass();
        }, true);

        $instance1 = IoC::Resolve('singleton');

        $instance2 = IoC::Resolve('singleton');

        $this->assertSame($instance1, $instance2);
    }

    public function testNestedScopes()
    {
        IoC::Resolve('Scopes.New', 'parentScope');
        IoC::Resolve('Scopes.New', 'childScope', 'parentScope');

        IoC::Resolve('Scopes.Current', 'childScope');
        IoC::Resolve('IoC.Register', 'nested', function () {
            return new stdClass();
        });

        $instance = IoC::Resolve('nested');
        $this->assertInstanceOf(stdClass::class, $instance);
    }

    public function testExceptionOnMissingDependency()
    {
        $this->expectException(\Exception::class);
        IoC::Resolve('missing');
    }

    public function testExceptionOnInvalidScope()
    {
        $this->expectException(\Exception::class);
        IoC::Resolve('Scopes.Current', 'invalidScope');
    }

    public function testScopes()
    {
        IoC::Resolve('Scopes.New', 'scope1');

        IoC::Resolve('Scopes.Current', 'scope1');

        IoC::Resolve('IoC.Register', 'scoped', function () {
            return new stdClass();
        }, true);

        $instance1 = IoC::Resolve('scoped');
        $this->assertInstanceOf(stdClass::class, $instance1);

        IoC::Resolve('Scopes.Current', 'default');

        IoC::Resolve('scoped');
    }

    public function testInvalidScope()
    {
        $this->expectException(\Exception::class);
        IoC::Resolve('Scopes.Current', 'invalidScope');
    }
}