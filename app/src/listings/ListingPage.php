<?php

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\DropdownField;

class ListingPage extends Page
{
    private static $db = [
        // main data
        'Date' => 'Date', // date listing was created

        'Availability' => 'Boolean',
        'DateAvailable' => 'Date', // date it will be available IF not available yet

        'Address' => 'Text',
        'Cost' => 'Int',
        'Bedrooms' => 'Int',
        'Bathrooms' => 'Int',
        'Carparks' => 'Int',
        'FloorSpace' => 'Int',
        'LandArea' => 'Int',
        'SchoolZone' => 'Varchar(255)',
        'YearMade' => 'Int',

        // features
        'OpenPlan' => 'Boolean',
        'HasVideo' => 'Boolean',
        'HouseTypeHouse' => 'Boolean',
        'HouseTypeTownhouse' => 'Boolean',
        'IsFenced' => 'Boolean',

        // MJ Home quality features
        'HasHeatPump' => 'Boolean',
        'HasDeckArea' => 'Boolean',
        'HasGardenArea' => 'Boolean',

        // furnishing
        'QualityAppliances' => 'Boolean',
        'HasAC' => 'Boolean',
        'IsFurnished' => 'Boolean'
    ];

    private static $has_many = [
        'ListingImageObjects' => ListingImageObject::class // ListingImageObject.php
    ];

    private static $has_one = [
        'ContactObject' => ContactObject::class // ContactObject.php
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // Images tab
        $fields->addFieldToTab(
            'Root.Images',
            GridField::create(
                'ListingImageObjects',
                'Images',
                $this->ListingImageObjects(),
                GridFieldConfig_RecordEditor::create()
            )
        );

        // main fields
        // add main fields (Root.Main)
        $fields->addFieldToTab('Root.Main', DateField::create('Date', 'Date listing was created'));
        $fields->addFieldToTab('Root.Main', CheckboxField::create('Availability', 'Currently available'));
        $fields->addFieldToTab('Root.Main', DateField::create('DateAvailable', 'Date to be avaiable (if not yet available)'));
        $fields->addFieldToTab('Root.Main', TextField::create('Address', 'Address'));

        $fields->addFieldToTab('Root.Main', NumericField::create('Cost', 'Rent Cost per Week'));
        $fields->addFieldToTab('Root.Main', NumericField::create('Bedrooms', 'Number of Bedrooms'));
        $fields->addFieldToTab('Root.Main', NumericField::create('Bathrooms', 'Number of Bathrooms'));
        $fields->addFieldToTab('Root.Main', NumericField::create('Carparks', 'Number of Carparks'));
        $fields->addFieldToTab('Root.Main', NumericField::create('FloorSpace', 'Floor space (m^2)'));
        $fields->addFieldToTab('Root.Main', NumericField::create('LandArea', 'Land area (m^2)'));
        $fields->addFieldToTab('Root.Main', NumericField::create('YearMade', 'Year Built'));

        // Images tab
        $fields->addFieldToTab(
            'Root.Images',
            GridField::create(
                'ListingImageObjects',
                'Images',
                $this->ListingImageObjects(),
                GridFieldConfig_RecordEditor::create()
            )
        );

        // features tab
        $fields->addFieldToTab(
            'Root.Features',
            FieldGroup::create(
                'General Features',
                CheckboxField::create('OpenPlan', 'Open Plan'),
                CheckboxField::create('HasVideo', 'Has Video'),
                CheckboxField::create('IsFenced', 'Fully Fenced')
            )
        );

        $fields->addFieldToTab(
            'Root.Features',
            FieldGroup::create(
                'House Type',
                CheckboxField::create('HouseTypeHouse', 'House'),
                CheckboxField::create('HouseTypeTownhouse', 'Townhouse')
            )
        );

        // MJ Home quality features
        $fields->addFieldToTab(
            'Root.Quality',
            FieldGroup::create(
                'Quality Features',
                CheckboxField::create('HasHeatPump', 'Heat Pump'),
                CheckboxField::create('HasDeckArea', 'Deck Area'),
                CheckboxField::create('HasGardenArea', 'Garden Area')
            )
        );

        // furnishing
        $fields->addFieldToTab(
            'Root.Furnishing',
            FieldGroup::create(
                'Furnishing & Appliances',
                CheckboxField::create('QualityAppliances', 'Quality Appliances'),
                CheckboxField::create('HasAC', 'Air Conditioning'),
                CheckboxField::create('IsFurnished', 'Fully Furnished')
            )
        );
        // contact
        $fields->addFieldToTab(
            'Root.Contact',
            DropdownField::create(
                'ContactObjectID',
                'Assigned Agent',
                ContactObject::get()->map('ID', 'ContactName') 
            )->setEmptyString('-- Select an Agent --')
        );

        return $fields;
    }
}

class ListingPageController extends PageController {}