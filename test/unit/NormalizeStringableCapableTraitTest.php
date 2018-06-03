<?php

namespace Dhii\Util\Normalization\UnitTest;

use Dhii\Util\Normalization\NormalizeStringableCapableTrait as TestSubject;
use Dhii\Util\String\StringableInterface as Stringable;
use InvalidArgumentException;
use stdClass;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_MockObject_MockBuilder as MockBuilder;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class NormalizeStringableCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Util\Normalization\NormalizeStringableCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @param array $methods The methods to mock.
     *
     * @return MockObject The new instance.
     */
    public function createInstance($methods = [])
    {
        is_array($methods) && $methods = $this->mergeValues($methods, [
            '_createInvalidArgumentException',
            '__',
        ]);

        $mock = $this->getMockBuilder(static::TEST_SUBJECT_CLASSNAME)
            ->setMethods($methods)
            ->getMockForTrait();

        $mock->method('__')
            ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * Merges the values of two arrays.
     *
     * The resulting product will be a numeric array where the values of both inputs are present, without duplicates.
     *
     * @since [*next-version*]
     *
     * @param array $destination The base array.
     * @param array $source      The array with more keys.
     *
     * @return array The array which contains unique values
     */
    public function mergeValues($destination, $source)
    {
        return array_keys(array_merge(array_flip($destination), array_flip($source)));
    }

    /**
     * Creates a mock that both extends a class and implements interfaces.
     *
     * This is particularly useful for cases where the mock is based on an
     * internal class, such as in the case with exceptions. Helps to avoid
     * writing hard-coded stubs.
     *
     * @since [*next-version*]
     *
     * @param string   $className      Name of the class for the mock to extend.
     * @param string[] $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return MockBuilder The builder for a mock of an object that extends and implements
     *                     the specified class and interfaces.
     */
    public function mockClassAndInterfaces($className, $interfaceNames = [])
    {
        $paddingClassName = uniqid($className);
        $definition = vsprintf('abstract class %1$s extends %2$s implements %3$s {}', [
            $paddingClassName,
            $className,
            implode(', ', $interfaceNames),
        ]);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a mock that uses traits.
     *
     * This is particularly useful for testing integration between multiple traits.
     *
     * @since [*next-version*]
     *
     * @param string[] $traitNames Names of the traits for the mock to use.
     *
     * @return MockBuilder The builder for a mock of an object that uses the traits.
     */
    public function mockTraits($traitNames = [])
    {
        $paddingClassName = uniqid('Traits');
        $definition = vsprintf('abstract class %1$s {%2$s}', [
            $paddingClassName,
            implode(
                ' ',
                array_map(
                    function ($v) {
                        return vsprintf('use %1$s;', [$v]);
                    },
                    $traitNames)),
        ]);
        var_dump($definition);
        eval($definition);

        return $this->getMockBuilder($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException|MockObject The new exception.
     */
    public function createException($message = '')
    {
        $mock = $this->getMockBuilder('Exception')
            ->setConstructorArgs([$message])
            ->getMock();

        return $mock;
    }

    /**
     * Creates a validation failed exception for testing purposes.
     *
     * @since [*next-version*]
     *
     * @param string $message The error message.
     *
     * @return InvalidArgumentException The new exception.
     */
    public function createInvalidArgumentException($message = '')
    {
        $mock = $this->mock('InvalidArgumentException')
            ->new($message);

        return $mock;
    }

    /**
     * Creates a stringable.
     *
     * @since [*next-version*]
     *
     * @param string $string The string that the stringable should represent.
     *
     * @return Stringable|MockObject The new stringable
     */
    public function createStringable($string = '')
    {
        $mock = $this->getMock('Dhii\Util\String\StringableInterface');
        $mock->method('__toString')
            ->will($this->returnCallback(function () use ($string) {
                return $string;
            }));

        return $mock;
    }

    /**
     * Tests whether a valid instance of the test subject can be created.
     *
     * @since [*next-version*]
     */
    public function testCanBeCreated()
    {
        $subject = $this->createInstance();

        $this->assertInternalType(
            'object',
            $subject,
            'A valid instance of the test subject could not be created.'
        );
    }

    /**
     * Tests whether `_normalizeStringable()` works as expected when given a stringable object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessStringable()
    {
        $stringable = $this->createStringable();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeStringable($stringable);
        $this->assertSame($stringable, $result, 'Stringable normalization result is wrong');
    }

    /**
     * Tests whether `_normalizeStringable()` works as expected when given a string.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessString()
    {
        $stringable = uniqid('stringable');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeStringable($stringable);
        $this->assertEquals($stringable, $result, 'String normalization result is wrong');
    }

    /**
     * Tests whether `_normalizeStringable()` works as expected when given an integer.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessInt()
    {
        $stringable = rand(0, 99);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeStringable($stringable);
        $this->assertEquals($stringable, $result, 'Integer normalization result is wrong');
    }

    /**
     * Tests whether `_normalizeStringable()` works as expected when given a float.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessFloat()
    {
        $stringable = rand(0, 9999) / 100.00;
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeStringable($stringable);
        $this->assertEquals($stringable, $result, 'Float normalization result is wrong');
    }

    /**
     * Tests whether `_normalizeStringable()` works as expected when given a boolean.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessBool()
    {
        $stringable = (bool) rand(0, 1);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeStringable($stringable);
        $this->assertEquals($stringable, $result, 'Boolean normalization result is wrong');
    }

    /**
     * Tests whether `_normalizeStringable()` fails as expected when given an array.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessArray()
    {
        $stringable = array_fill(0, rand(1, 9), uniqid('item'));
        $exception = $this->createInvalidArgumentException('Not a stringable');
        $subject = $this->createInstance(['_createInvalidArgumentException']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_createInvalidArgumentException')
            ->with(
                $this->isType('string'),
                $this->anything(),
                $this->anything(),
                $stringable
            )
            ->will($this->returnValue($exception));

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeStringable($stringable);
    }

    /**
     * Tests whether `_normalizeStringable()` fails as expected when given an array.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessObject()
    {
        $stringable = new stdClass();
        $exception = $this->createInvalidArgumentException('Not a stringable');
        $subject = $this->createInstance(['_createInvalidArgumentException']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_createInvalidArgumentException')
            ->with(
                $this->isType('string'),
                $this->anything(),
                $this->anything(),
                $stringable
            )
            ->will($this->returnValue($exception));

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeStringable($stringable);
    }

    /**
     * Tests whether `_normalizeStringable()` fails as expected when given an array.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringableSuccessNull()
    {
        $stringable = null;
        $exception = $this->createInvalidArgumentException('Not a stringable');
        $subject = $this->createInstance(['_createInvalidArgumentException']);
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
            ->method('_createInvalidArgumentException')
            ->with(
                $this->isType('string'),
                $this->anything(),
                $this->anything(),
                $stringable
            )
            ->will($this->returnValue($exception));

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeStringable($stringable);
    }
}
