<?php

namespace BarcodeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BarcodeBundle extends Bundle
{
    public function getParent()
    {
        return 'HackzillaBarcodeBundle';
    }
}
