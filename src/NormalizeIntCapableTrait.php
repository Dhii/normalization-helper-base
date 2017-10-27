<?php

namespace Dhii\Util\Normalization;

use Dhii\Util\String\StringableInterface as Stringable;
use Exception as RootException;
use InvalidArgumentException;

/**
 * Functionality for integer normalization.
 *
 * @since [*next-version*]
 */
trait NormalizeIntCapableTrait
{
    /**
     * Normalizes a value into an integer.
     *
     * The value must be a whole number, or a string representing such a number,
     * or an object representing such a string.
     *
     * @since [*next-version*]
     *
     * @param mixed $value The value to normalize.
     *
     * @throws InvalidArgumentException If value cannot be normalized.
     *
     * @return int The normalized value.
     */
    protected function _normalizeInt($value)
    {
        $origValue = $value;
        if ($value instanceof Stringable) {
            $value = $this->_normalizeString($value);
        }

        if (!is_numeric($value)) {
            throw $this->_createInvalidArgumentException($this->__('Not a number'), null, null, $origValue);
        }

        $value += 0;

        if (fmod($value, 1) !== 0.00) {
            throw $this->_createInvalidArgumentException($this->__('Not a whole number'), null, null, $origValue);
        }

        $value = (int) $value;

        return $value;
    }

    /**
     * Normalize a value to its string representation.
     *
     * @since [*next-version*]
     *
     * @param mixed The value to normalize.
     *
     * @return string The string representation of the value.
     */
    abstract protected function _normalizeString($value);

    /**
     * Creates a new invalid argument exception.
     *
     * @since [*next-version*]
     *
     * @param string|Stringable|null $message  The error message, if any.
     * @param int|null               $code     The error code, if any.
     * @param RootException|null     $previous The inner exception for chaining, if any.
     * @param mixed|null             $argument The invalid argument, if any.
     *
     * @return InvalidArgumentException The new exception.
     */
    abstract protected function _createInvalidArgumentException(
        $message = null,
        $code = null,
        RootException $previous = null,
        $argument = null
    );

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see   sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = [], $context = null);
}
