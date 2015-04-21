<?php


namespace AppBundle\Services\Twig;


use AppBundle\Services\Enum\BadgeCategory;
use AppBundle\Services\Enum\BadgeState;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use AppBundle\Services\Enum\Unit;

class EnumExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [new \Twig_SimpleFilter('enum', [$this, 'enum_filter'])];
    }

    public function getGlobals()
    {
        return ['Enum' => array_map([$this, 'process_enum_global'], [
            'bowtype' => BowType::$choices,
            'gender' => Gender::$choices,
            'skill' => Skill::$choices,
            'unit' => Unit::$choices,
            'badgecat' => BadgeCategory::$choices,
            'badgestate' => BadgeState::$choices,
        ])];
    }

    private function process_enum_global($enum)
    {
        array_walk($enum, function (&$v, $k) {
            $v = $k;
        });

        return $enum;
    }

    public function enum_filter($value, $enum)
    {
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
