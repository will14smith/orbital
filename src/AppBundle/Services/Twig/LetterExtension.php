<?php

namespace AppBundle\Services\Twig;

class LetterExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('toLetter', [$this, 'letter_filter'])];
    }

    public function letter_filter($value)
    {
        $value = intval($value);

        return chr(ord('A') + $value - 1);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_letter_ext';
    }
}
