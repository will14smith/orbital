<?php

namespace AppBundle\Services\Twig;

class ArrayExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('remove', [$this, 'remove'])];
    }

    public function remove($array, $key, $value)
    {
        if (!is_array($array)) {
            throw new \Exception('Input not an array');
        }

        if (substr($key, -2) === '[]') {
            $key = substr($key, 0, strlen($key) - 2);
        }

        if (!array_key_exists($key, $array) || !is_array($array[$key])) {
            return $array;
        }

        if (($subKey = array_search($value, $array[$key])) !== false) {
            unset($array[$key][$subKey]);
        }

        return $array;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_array_ext';
    }
}
