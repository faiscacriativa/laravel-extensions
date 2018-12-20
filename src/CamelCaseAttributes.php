<?php

/**
 * PHP Version 7.2
 *
 * @category Traits
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/CamelCaseAttributes.php
 * @see      https://stackoverflow.com/questions/25559558/how-can-i-access-attributes-using-camel-case
 */
namespace FaiscaCriativa\LaravelExtensions;

/**
 * Allow access model attributes with camel case.
 *
 * @category Traits
 * @package  LaravelExtensions
 * @author   Faísca Criativa <developers@faiscacriativa.com.br>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/faiscacriativa/laravel-extensions/blob/master/src/CamelCaseAttributes.php
 * @see      https://stackoverflow.com/questions/25559558/how-can-i-access-attributes-using-camel-case
 */
trait CamelCaseAttributes
{
    /**
     * Get model attribute value.
     *
     * @param string $key The attribute to be get.
     *
     * @return mixed
     */
    public function getAttribute($key)
    {

        if (array_key_exists($key, $this->relations) || method_exists($this, $key)) {
            return parent::getAttribute($key);
        } else {
            return parent::getAttribute(snake_case($key));
        }
    }

    /**
     * Set model attribute value.
     *
     * @param string $key   The attribute to be set.
     * @param mixed  $value The value to be set.
     *
     * @return mixed;
     */
    public function setAttribute($key, $value)
    {
        return parent::setAttribute(snake_case($key), $value);
    }

}
