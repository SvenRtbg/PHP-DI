<?php

namespace DI\Test\UnitTest\Definition;

use DI\Definition\AutowireDefinition;
use DI\Definition\ObjectDefinition\MethodInjection;
use DI\Definition\ObjectDefinition\PropertyInjection;
use DI\Definition\ValueDefinition;

/**
 * @covers \DI\Definition\AutowireDefinition
 */
class AutowireDefinitionTest extends ObjectDefinitionTest
{
    /**
     * @test
     */
    public function should_only_merge_with_object_subdefinitions()
    {
        $definition = new AutowireDefinition('foo', 'bar');
        $definition->setSubDefinition(new ValueDefinition('bar', 'Hello'));

        // Unchanged definition
        $this->assertEquals(new AutowireDefinition('foo', 'bar'), $definition);
    }

    /**
     * @test
     */
    public function should_merge_with_its_subdefinition()
    {
        $definition = new AutowireDefinition('foo', 'bar');
        $definition->addPropertyInjection(new PropertyInjection('property1', 'Property1'));
        $definition->addPropertyInjection(new PropertyInjection('property2', 'Property2'));
        $definition->addMethodInjection(new MethodInjection('method1', ['foo']));
        $definition->addMethodInjection(new MethodInjection('method2'));

        $subDefinition = new AutowireDefinition('bar');
        $subDefinition->setLazy(true);
        $subDefinition->setConstructorInjection(MethodInjection::constructor());
        $subDefinition->addPropertyInjection(new PropertyInjection('property1', 'Property1'));
        $subDefinition->addPropertyInjection(new PropertyInjection('property3', 'Property3'));
        $subDefinition->addMethodInjection(new MethodInjection('method1', ['bar']));
        $subDefinition->addMethodInjection(new MethodInjection('method3'));

        $definition->setSubDefinition($subDefinition);

        $this->assertEquals('foo', $definition->getName());
        $this->assertEquals('bar', $definition->getClassName());
        $this->assertTrue($definition->isLazy());
        $this->assertNotNull($definition->getConstructorInjection());
        $this->assertCount(3, $definition->getPropertyInjections());
        $this->assertCount(3, $definition->getMethodInjections());
        $this->assertEquals([
            new MethodInjection('method1', ['foo']),
            new MethodInjection('method2'),
            new MethodInjection('method3'),
        ], $definition->getMethodInjections());
    }

    /**
     * @test
     */
    public function should_merge_multiple_method_calls()
    {
        $definition = new AutowireDefinition('foo');
        $definition->addMethodInjection(new MethodInjection('method1'));
        $definition->addMethodInjection(new MethodInjection('method2', ['bam']));
        $definition->addMethodInjection(new MethodInjection('method2', ['baz']));

        $subDefinition = new AutowireDefinition('bar');
        $subDefinition->addMethodInjection(new MethodInjection('method1', ['bar']));
        $subDefinition->addMethodInjection(new MethodInjection('method2', ['foo', 'bar']));
        $subDefinition->addMethodInjection(new MethodInjection('method3'));
        $subDefinition->addMethodInjection(new MethodInjection('method3'));

        $definition->setSubDefinition($subDefinition);

        $this->assertEquals([
            new MethodInjection('method1', ['bar']),
            new MethodInjection('method2', ['bam', 'bar']),
            new MethodInjection('method2', ['baz']),
            new MethodInjection('method3'),
            new MethodInjection('method3'),
        ], $definition->getMethodInjections());
    }
}