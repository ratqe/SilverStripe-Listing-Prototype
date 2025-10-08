<?php

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\FieldType\DBDecimal;
use SilverStripe\Core\Environment;
use SilverStripe\ORM\ValidationException;

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
        'IsFurnished' => 'Boolean',

        // location
        'Latitude'  => 'Decimal(10,8)',
        'Longitude' => 'Decimal(11,8)',
    ];

    private static $has_many = [
        'ListingImageObjects' => ListingImageObject::class, // ListingImageObject.php
        'ListingFloorPlans' => ListingFloorPlanObject::class
    ];

    private static $many_many = [
        'Contacts' => ContactObject::class
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // main fields
        // add main fields (Root.Main)
        $fields->addFieldToTab('Root.Main', DateField::create('Date', 'Date listing was created'));
        $fields->addFieldToTab('Root.Main', CheckboxField::create('Availability', 'Currently available'));
        $fields->addFieldToTab('Root.Main', DateField::create('DateAvailable', 'Date to be available (if not yet available)'));
        $fields->addFieldToTab('Root.Main', TextField::create('Address', 'Address')
            ->setDescription('Type a street address (e.g. "1 Queen Street, Auckland"). If lat/lng are blank, they will auto-fill on save.'));

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

        // floor plan
        $fields->addFieldToTab(
            'Root.Images',
            GridField::create(
                'ListingFloorPlans',
                'Floor Plans',
                $this->ListingFloorPlans(),
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
        /*
        // contact
        $fields->addFieldToTab(
            'Root.Contact',
            DropdownField::create(
                'ContactObjectID',
                'Assigned Agent',
                ContactObject::get()->map('ID', 'ContactName') 
            )->setEmptyString('-- Select an Agent --')
        );
        */

        // contacts
        $fields->addFieldToTab(
            'Root.Contacts',
            GridField::create(
                'Contacts',
                'Agents / Contacts',
                $this->Contacts(),
                GridFieldConfig_RelationEditor::create()
            )
        );

        // maps
        $fields->addFieldsToTab('Root.Location', [
            TextField::create('Latitude', 'Latitude (e.g. -36.848461)')
                ->setDescription('Optional. Leave blank to auto-fill from Address.'),

            TextField::create('Longitude', 'Longitude (e.g. 174.763336)')
                ->setDescription('Optional. Leave blank to auto-fill from Address.'),
        ]);


        return $fields;
    }


    /** Treats 0,0 as "no coords" */
    public function HasCoords(): bool
    {
        $lat = (float)$this->Latitude;
        $lng = (float)$this->Longitude;
        return $lat !== 0.0 && $lng !== 0.0;
    }

    /** Handy link for fallback/preview */
    public function GoogleMapsLink(): ?string
    {
        if ($this->HasCoords()) {
            return sprintf('https://maps.google.com/?q=%s,%s', $this->Latitude, $this->Longitude);
        }
        if ($this->Address) {
            return 'https://maps.google.com/?q=' . urlencode($this->Address ?? '');
        }
        return null;
    }

 /**
     * Auto-geocode on save if address changed and coords are blank,
     * and throw a ValidationException if invalid.
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $addressChanged = $this->isChanged('Address', 2);
        $coordsMissing  = !$this->HasCoords();

        // Only attempt if there is an address
        if ($this->Address) {
            // If no coords provided, try to geocode
            if (($addressChanged || $coordsMissing) && $coordsMissing) {
                $coords = $this->geocodeAddress($this->Address);

                if (!$coords) {
                    throw new ValidationException(
                        _t(__CLASS__ . '.INVALID_ADDRESS', 
                            'Unable to find a valid location for the given address. Please check and try again.')
                    );
                }

                $this->Latitude  = $coords['lat'];
                $this->Longitude = $coords['lng'];
            }
        } else {
            // No address provided, ensure coordinates aren't blank
            if ($coordsMissing) {
                throw new ValidationException(
                    _t(__CLASS__ . '.MISSING_ADDRESS', 
                        'You must provide either a valid address or latitude/longitude.')
                );
            }
        }
    }

    /**
     * Geocode using OpenStreetMap Nominatim, with error checking.
     */
    private function geocodeAddress(string $address): ?array
    {
        $email = Environment::getEnv('NOMINATIM_EMAIL') ?: 'maps@mydomain.nz';

        $query = http_build_query([
            'format'       => 'jsonv2',
            'limit'        => 1,
            'q'            => $address,
            'countrycodes' => 'nz',
        ]);

        $url = "https://nominatim.openstreetmap.org/search?$query";

        $ctx = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'header'  => "User-Agent: PropertyListingSite/1.0 ($email)\r\nAccept: application/json\r\n",
                'timeout' => 8,
            ]
        ]);

        $json = @file_get_contents($url, false, $ctx);

        if ($json === false) {
            return null; // request failed
        }

        $data = json_decode($json, true);

        if (
            !is_array($data) ||
            empty($data[0]['lat']) ||
            empty($data[0]['lon']) ||
            !is_numeric($data[0]['lat']) ||
            !is_numeric($data[0]['lon'])
        ) {
            return null;
        }

        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon'],
        ];
    }
}

class ListingPageController extends PageController {}

