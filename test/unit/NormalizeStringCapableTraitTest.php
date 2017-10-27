<?php

namespace Dhii\Util\Normalization\UnitTest;

use Xpmock\TestCase;
use InvalidArgumentException;
use Dhii\Util\Normalization\NormalizeStringCapableTrait as TestSubject;
use Dhii\Util\String\StringableInterface as Stringable;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class NormalizeStringCapableTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Util\Normalization\NormalizeStringCapableTrait';

    /**
     * Creates a new instance of the test subject.
     *
     * @since [*next-version*]
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function createInstance()
    {
        $mock = $this->getMockForTrait(static::TEST_SUBJECT_CLASSNAME);
        $mock->method('_createInvalidArgumentException')
                ->will($this->returnCallback(function ($message) {
                    return $this->createInvalidArgumentException($message);
                }));
        $mock->method('__')
                ->will($this->returnArgument(0));

        return $mock;
    }

    /**
     * Creates a stringable.
     *
     * @since [*next-version*]
     *
     * @param string $string The string that the stringable should represent.
     *
     * @return Stringable The new stringable
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
        $mock = $this->mock('InvalidArgumentException')
                ->new($message);

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
     * Tests that `_normalizeString()` method works as expected when normalizing a string.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringString()
    {
        $data = uniqid('string-');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeString($data);
        $this->assertEquals($data, $result, 'The stringable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing a stringable object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringStringable()
    {
        $data = uniqid('string-');
        $stringable = $this->createStringable($data);
        $stringable->expects($this->exactly(1))
                ->method('__toString');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeString($stringable);
        $this->assertEquals($data, $result, 'The stringable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing a scalar integer.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringInteger()
    {
        $data = rand(1, 100);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeString($data);
        $this->assertEquals((string) $data, $result, 'The stringable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing a float.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringFloat()
    {
        $data = rand(1, 100) - (rand(1, 99) * 0.01);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeString($data);
        $this->assertEquals((string) $data, $result, 'The stringable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing a boolean.
     *
     * @since [*next-version*]
     */
    public function testNormalizeStringBool()
    {
        $data = (bool) rand(0, 1);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeString($data);
        $this->assertEquals((string) $data, $result, 'The stringable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing a null.
     *
     * @since [*next-version*]
     */
    public function testNormalizeNull()
    {
        $data = null;
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeString($data);
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing an object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeObject()
    {
        $data = new \stdClass();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeString($data);
    }

    /**
     * Tests that `_normalizeString()` method works as expected when normalizing an object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeArray()
    {
        $data = range(0, 9);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeString($data);
    }
}
