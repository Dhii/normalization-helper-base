<?php

namespace Dhii\Util\Normalization\UnitTest;

use Dhii\Util\Normalization\NormalizeIterableCapableTrait as TestSubject;
use InvalidArgumentException;
use Traversable;
use Xpmock\TestCase;
use Exception as RootException;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class NormalizeIterableCapableTraitTest extends TestCase
{
    /**
     * The class name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Util\Normalization\NormalizeIterableCapableTrait';

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
        $methods = $this->mergeValues($methods, [
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
     * @param string $className      Name of the class for the mock to extend.
     * @param string $interfaceNames Names of the interfaces for the mock to implement.
     *
     * @return object The object that extends and implements the specified class and interfaces.
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

        return $this->getMockForAbstractClass($paddingClassName);
    }

    /**
     * Creates a new exception.
     *
     * @since [*next-version*]
     *
     * @param string $message The exception message.
     *
     * @return RootException The new exception.
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
     * @return InvalidArgumentException
     */
    public function createInvalidArgumentException($message = '')
    {
        $mock = $this->getMockBuilder('InvalidArgumentException')
                ->setConstructorArgs([$message])
                ->getMock();

        return $mock;
    }

    /**
     * Creates a new traversable.
     *
     * @since [*next-version*]
     *
     * @return MockObject|Traversable
     */
    public function createTraversable()
    {
        $mock = $this->getMockBuilder('ArrayIterator')
                ->getMock();

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
     * Test that `_normalizeIterable()` works as expected when given an array.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIterableArray()
    {
        $iterable = [uniqid('key'), uniqid('val')];
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeIterable($iterable);
        $this->assertEquals($iterable, $result, 'Array could not be normalized correctly');
    }

    /**
     * Test that `_normalizeIterable()` works as expected when given a traversable.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIterableTraversable()
    {
        $iterable = $this->createTraversable();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeIterable($iterable);
        $this->assertEquals($iterable, $result, 'Traversable could not be normalized correctly');
    }

    /**
     * Test that `_normalizeIterable()` works as expected when given a plain object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIterableObject()
    {
        $iterable = new \stdClass();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeIterable($iterable);
        $this->assertEquals($iterable, $result, 'Plain object could not be normalized correctly');
    }

    /**
     * Test that `_normalizeIterable()` fails as expected when it cannot normalize.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIterableFailureCannotNormalize()
    {
        $iterable = uniqid('string');
        $exception = $this->createInvalidArgumentException();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $subject->expects($this->exactly(1))
                ->method('_createInvalidArgumentException')
                ->with(
                    $this->isType('string'),
                    null,
                    null,
                    $iterable
                )
                ->will($this->returnValue($exception));

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeIterable($iterable);
    }
}
