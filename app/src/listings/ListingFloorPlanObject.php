<?php

use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\AssetAdmin\Forms\UploadField;

class ListingFloorPlanObject extends DataObject
{
    private static $db = [
        'Caption' => 'Varchar(255)'
    ];

    private static $has_one = [
        'ImageFile' => Image::class,
        'ListingPage' => ListingPage::class
    ];

    private static $owns = [
        'ImageFile'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        
        $fields = FieldList::create(
            UploadField::create('ImageFile', 'Upload Image'),
            \SilverStripe\Forms\TextField::create('Caption')
        );
        return $fields;
    }
}
