<?php

namespace Illum\Validation;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;
use Illum\Validation\Rules\Dimensions;
use Illum\Validation\Rules\In;
use Illum\Validation\Rules\NotIn;
use Illum\Validation\Rules\RequiredIf;

class Rule
{
    use Macroable;

    /**
     * Create a new conditional rule set.
     *
     * @param  callable|bool  $condition
     * @param  array|string  $rules
     * @param  array|string  $defaultRules
     * @return \Illum\Validation\ConditionalRules
     */
    public static function when($condition, $rules, $defaultRules = [])
    {
        return new ConditionalRules($condition, $rules, $defaultRules);
    }

    /**
     * Get a dimensions constraint builder instance.
     *
     * @param  array  $constraints
     * @return \Illum\Validation\Rules\Dimensions
     */
    public static function dimensions(array $constraints = [])
    {
        return new Dimensions($constraints);
    }

    /**
     * Get an in constraint builder instance.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array|string  $values
     * @return \Illum\Validation\Rules\In
     */
    public static function in($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new In(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a not_in constraint builder instance.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable|array|string  $values
     * @return \Illum\Validation\Rules\NotIn
     */
    public static function notIn($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new NotIn(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a required_if constraint builder instance.
     *
     * @param  callable|bool  $callback
     * @return \Illum\Validation\Rules\RequiredIf
     */
    public static function requiredIf($callback)
    {
        return new RequiredIf($callback);
    }
}
