<?php


namespace AppBundle\Services\Twig;


use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use AppBundle\Services\Enum\Unit;

class EnumExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('enum', array($this, 'enum_filter'))];
    }

    public function getGlobals()
    {
        return ['Enum' => [
            'bowtype' => BowType::$choices,
            'gender' => Gender::$choices,
            'skill' => Skill::$choices,
            'unit' => Unit::$choices,
        ]];
    }

    public function enum_filter($value, $enum) {
        //TODO check exists?
        return $this->getGlobals()['Enum'][$enum][$value];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'app_enum_ext';
    }
}