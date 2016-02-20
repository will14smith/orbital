<?php

namespace AppBundle\Services\Twig;

use AppBundle\Constants;

class ConstantExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    public function getGlobals()
    {
        return [
            'DATE_FORMAT' => Constants::DATE_FORMAT,
            'DATETIME_FORMAT' => Constants::DATETIME_FORMAT
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_constant_ext';
    }
}
