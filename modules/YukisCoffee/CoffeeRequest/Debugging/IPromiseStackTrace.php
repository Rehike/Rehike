<?php
namespace YukisCoffee\CoffeeRequest\Debugging;

/**
 * An object providing access to stack traces for debugging a Promise.
 * 
 * @author Taniko Yamamoto <kirasicecreamm@gmail.com>
 */
interface IPromiseStackTrace
{
    public function __toString();

    /**
     * Gets the simplified trace as an array.
     */
    public function getTraceAsArray(): array;

    /**
     * Gets the simplified trace as a string.
     */
    public function getTraceAsString(): string;

    /**
     * Gets the original (advanced) trace as an array.
     */
    public function getOriginalTraceAsArray(): array;

    /**
     * Gets the original (advanced) trace as a string.
     */
    public function getOriginalTraceAsString(): string;
}