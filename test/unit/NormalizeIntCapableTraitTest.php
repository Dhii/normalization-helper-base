<?php

namespace Dhii\Util\Normalization\UnitTest;

use Xpmock\TestCase;
use InvalidArgumentException;
use Dhii\Util\Normalization\NormalizeIntCapableTrait as TestSubject;
use Dhii\Util\String\StringableInterface as Stringable;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class NormalizeIntCapableTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Util\Normalization\NormalizeIntCapableTrait';

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
        $mock->method('_normalizeString')
                ->will($this->returnCallback(function ($val) {
                    return (string) $val;
                }));

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
     * Tests that `_normalizeInt()` method works as expected when normalizing a numeric.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIntString()
    {
        $data = rand(1, 99);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeInt((string) $data);
        $this->assertEquals($data, $result, 'The string was not normalized correctly');
    }

    /**
     * Tests that `_normalizeInt()` method fails when normalizing a non-numeric string.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIntStringFailure()
    {
        $data = uniqid('string-');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeInt($data);
    }

    /**
     * Tests that `_normalizeInt()` method works as expected when normalizing a stringable object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIntStringable()
    {
        $data = rand(1, 99);
        $stringable = $this->createStringable((string) $data);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeInt($stringable);
        $this->assertEquals($data, $result, 'The stringable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeInt()` method works as expected when normalizing a scalar integer.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIntInteger()
    {
        $data = rand(1, 99);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeInt($data);
        $this->assertEquals($data, $result, 'The integer was not normalized correctly');
    }

    /**
     * Tests that `_normalizeInt()` method works as expected when normalizing a float.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIntFloat()
    {
        $data = rand(1, 100) * 0.00;
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeInt($data);
        $this->assertEquals((int) $data, $result, 'The float was not normalized correctly');
    }

    /**
     * Tests that `_normalizeInt()` method fails when normalizing a non-whole float.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIntFloatFailure()
    {
        $data = rand(1, 100) - (rand(1, 99) * 0.01);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeInt($data);
    }

    /**
     * Tests that `_normalizeInt()` method fails when normalizing a non-whole float with floating number overflow.
     *
     * @since [*next-version*]
     */
    public function testNormalizeFloatOverflow()
    {
        $data = 4.000000000000002;
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $result = $_subject->_normalizeInt($data);
    }

    /**
     * Tests that `_normalizeInt()` method fails when normalizing a boolean.
     *
     * @since [*next-version*]
     */
    public function testNormalizeIntBool()
    {
        $data = (bool) rand(0, 1);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeInt($data);
    }

    /**
     * Tests that `_normalizeInt()` method works as expected when normalizing a null.
     *
     * @since [*next-version*]
     */
    public function testNormalizeNull()
    {
        $data = null;
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeInt($data);
    }

    /**
     * Tests that `_normalizeInt()` method works as expected when normalizing a non-stringable object.
     *
     * @since [*next-version*]
     */
    public function testNormalizeObject()
    {
        $data = new \stdClass();
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeInt($data);
    }

    /**
     * Tests that `_normalizeInt()` method works as expected when normalizing an array.
     *
     * @since [*next-version*]
     */
    public function testNormalizeArray()
    {
        $data = range(0, 9);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeInt($data);
    }
}
