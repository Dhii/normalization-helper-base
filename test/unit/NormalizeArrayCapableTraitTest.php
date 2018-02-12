<?php

namespace Dhii\Util\Normalization\UnitTest;

use Xpmock\TestCase;
use InvalidArgumentException;
use Dhii\Util\Normalization\NormalizeArrayCapableTrait as TestSubject;
use Traversable;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests {@see TestSubject}.
 *
 * @since [*next-version*]
 */
class NormalizeArrayCapableTraitTest extends TestCase
{
    /**
     * The name of the test subject.
     *
     * @since [*next-version*]
     */
    const TEST_SUBJECT_CLASSNAME = 'Dhii\Util\Normalization\NormalizeArrayCapableTrait';

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
     * Creates a traversable.
     *
     * @since [*next-version*]
     *
     * @param array $elements The elements that the traversable should iterate over.
     *
     * @return Traversable The new traversable.
     */
    public function createTraversable($elements = [])
    {
        $mock = $this->getMockBuilder('ArrayIterator')
                ->setConstructorArgs([$elements])
                ->setMethods(null)
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
     * Generates an array of random length containing random integers.
     *
     * @since [*next-version*]
     *
     * @param int $minAmount The minimal amount of elements in the array.
     * @param int $maxAmount The maximal amount of elements in the array.
     * @param int $min       The minimal integer.
     * @param int $max       The maximal integer.
     *
     * @return int[] The random array.
     */
    public function createRandomIntArray($minAmount, $maxAmount, $min, $max)
    {
        $amount = rand($minAmount, $maxAmount);
        $array = [];
        for ($i = 0; $i < $amount; ++$i) {
            $array[] = rand($min, $max);
        }

        return $array;
    }

    /**
     * Tests that `_normalizeArray()` method works as expected when normalizing an array.
     *
     * @since [*next-version*]
     */
    public function testNormalizeArrayArray()
    {
        $data = $this->createRandomIntArray(3, 10, 1, 99);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeArray($data);
        $this->assertEquals($data, $result, 'The array was not normalized correctly');
    }

    /**
     * Tests that `_normalizeArray()` method works as expected when normalizing an `stdClass` instance.
     *
     * @since [*next-version*]
     */
    public function testNormalizeArrayStdClass()
    {
        $data = [
            uniqid('key') => uniqid('val'),
            uniqid('key') => uniqid('val'),
            uniqid('key') => uniqid('val'),
        ];
        $iterable = (object) $data;
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeArray($iterable);
        $this->assertEquals($data, $result, 'The array was not normalized correctly');
    }

    /**
     * Tests that `_normalizeArray()` method works as expected when normalizing a {@see Traversable}.
     *
     * @since [*next-version*]
     */
    public function testNormalizeArrayTraversable()
    {
        $data = $this->createRandomIntArray(3, 10, 1, 99);
        $traversable = $this->createTraversable($data);
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $result = $_subject->_normalizeArray($traversable);
        $this->assertEquals($data, $result, 'The traversable was not normalized correctly');
    }

    /**
     * Tests that `_normalizeArray()` method fails when normalizing a string.
     *
     * @since [*next-version*]
     */
    public function testNormalizeArrayStringFailure()
    {
        $data = uniqid('string-');
        $subject = $this->createInstance();
        $_subject = $this->reflect($subject);

        $this->setExpectedException('InvalidArgumentException');
        $_subject->_normalizeArray($data);
    }
}
