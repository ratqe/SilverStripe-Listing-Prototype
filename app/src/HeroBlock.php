<?php

namespace App\Blocks;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\FieldList;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;

class HeroBlock extends BaseElement {
    private static $table_name = 'HeroBlock';
    private static $singular_name = 'Hero Block';
    private static $plural_name = 'Hero Blocks';

    private static $description = 'A large hero banner with headline, subtitle, and background image';

    private static $db = [
        'Headline' => 'Varchar(225)',
        'Subtitle' => 'Text',
    ];

    private static $has_one = [
        'BackgroundImage' => Image::class,
    ];

    private static $owns = [
        'BackgroundImage',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Headline', 'Headline'),
            TextareaField::create('Subtitle', 'Subtitle'),
            UploadField::create('BackgroundImage', 'Background Image')
                ->setAllowedFileCategories('image/supported')
                ->setIsMultiUpload(false)
        ]);

        return $fields;
    }

    public function getType()
    {
        return 'Hero';
    }
}