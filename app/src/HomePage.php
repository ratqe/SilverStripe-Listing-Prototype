<?php

namespace App\Pages;

use App\ElementalPage;
use DNADesign\Elemental\Models\ElementalArea;
/*
use DNADesign\Elemental\Forms\ElementalAreaField;
use SilverStripe\Forms\FieldList;
*/

class HomePage extends ElementalPage
{
    private static $table_name = 'HomePage';

    /*
    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        // Remove the content of WYSIWYG field
        $fields->removeByName('Content');

        // Add the Element editor
        $fields->addFieldToTab(
            'Root.Main',
            ElementalAreaField::create('ElementalArea')
        );

        return $fields;
    }
    */
}