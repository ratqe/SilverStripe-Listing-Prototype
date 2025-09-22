<?php

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBDecimal;
use SilverStripe\Core\Environment;

class PropertyPage extends SiteTree
{
    private static $table_name = 'PropertyPage';

    private static $db = [
        'Address'   => 'Varchar(255)',
        'Latitude'  => DBDecimal::class . '(10,8)',
        'Longitude' => DBDecimal::class . '(11,8)',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.Property', [
            TextField::create('Address', 'Address')
                ->setDescription('Type a street address (e.g. "1 Queen Street, Auckland"). If lat/lng are blank, they will auto-fill on save.'),

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
        return $lat !== 0.0 || $lng !== 0.0;
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

    /** Auto-geocode on save if Address changed and coords are blank/zero */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        $addressChanged = $this->isChanged('Address', 2); // 2 = check value change
        $coordsMissing  = !$this->HasCoords();

        if ($addressChanged && $coordsMissing && $this->Address) {
            if ($coords = $this->geocodeAddress($this->Address)) {
                $this->Latitude  = $coords['lat'];
                $this->Longitude = $coords['lng'];
            }
        }
    }

    /**
     * Geocode using OpenStreetMap Nominatim.
     */
    private function geocodeAddress(string $address): ?array
    {
        $email = Environment::getEnv('NOMINATIM_EMAIL') ?: 'maps@mydomain.nz';

        $query = http_build_query([
            'format'       => 'jsonv2',
            'limit'        => 1,
            'q'            => $address,
            'countrycodes' => 'nz', // adjust for your region, or remove
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
        if (!$json) {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data) || empty($data[0]['lat']) || empty($data[0]['lon'])) {
            return null;
        }

        return [
            'lat' => (float)$data[0]['lat'],
            'lng' => (float)$data[0]['lon'],
        ];
    }
}
