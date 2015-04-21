<?php


namespace AppBundle\Services\Twig;

use AppBundle\Services\Enum\Unit;

class MeasureExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('measure', [$this, 'measure_filter']),
            new \Twig_SimpleFilter('full_measure', [$this, 'full_measure_filter']),
        ];
    }

    public function full_measure_filter($object, $field_base, $field_value = 'Value', $field_unit = 'Unit')
    {
        return $this->measure_filter($object, $field_base, $field_value, $field_unit, true);
    }

    public function measure_filter($object, $field_base, $field_value = 'Value', $field_unit = 'Unit', $full = false)
    {
        $valueGetter = 'get' . ucfirst($field_base) . $field_value;
        $unitGetter = 'get' . ucfirst($field_base) . $field_unit;

        $value = $object->$valueGetter();
        $unit = $object->$unitGetter();

        if ($full) {
            $unit = Unit::$choices[$unit];

            if ($value != 1) {
                //TODO proper pluralisation
                $unit .= 's';
            }
        }

        return $value . ' ' . $unit;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_measure_ext';
    }
}
