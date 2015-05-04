<?php

namespace AppBundle\Services\Twig;

use Hackzilla\BarcodeBundle\Utility\Barcode;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BarcodeExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container) {

        $this->container = $container;
    }

    public function getFilters()
    {
        return [new \Twig_SimpleFilter('barcode', [$this, 'barcode_filter'], ['is_safe' => ['all']])];
    }

    public function barcode_filter($value, $type='html') {

        if(!is_int($value)) {
            throw new \Exception("Not currently implemented");
        }

        $code = str_pad($value, 12, "0", STR_PAD_LEFT);

        \ob_start();

        $barcode = $this->container->get('hackzilla_barcode');
        $barcode->setHeight(25);
        $barcode->setEncoding(Barcode::ENCODING_EAN);
        $barcode->setMode(Barcode::MODE_PNG);
        $barcode->outputImage($code);

        $contents = \ob_get_clean();

        return '<img src="data:image/png;base64,' . base64_encode($contents) . '">';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_barcode_ext';
    }
}
