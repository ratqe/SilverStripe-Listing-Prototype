<?php

namespace App;

use Page;
use DNADesign\Elemental\Extensions\ElementalPageExtension;

class ElementalPage extends Page
{
    private static $table_name = 'ElementalPage';
    
    private static $extensions = [
        ElementalPageExtension::class,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        return $fields;
    }
}
