<?php

use SilverStripe\ORM\DataObject;
use SilverStripe\Assets\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\AssetAdmin\Forms\UploadField;

class ListingImageObject extends DataObject
{
    private static $db = [
        'Caption' => 'Varchar(255)'
    ];

    private static $has_one = [
        'ImageFile' => File::class,
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
