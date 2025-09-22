<?php

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\View\Requirements;

class PropertyPageController extends ContentController
{
    protected function init()
    {
        parent::init();
        Requirements::insertHeadTags('<meta name="proof-requirements" content="yes">');


        // Leaflet (external)
        Requirements::css('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
        Requirements::javascript('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js');

        // Your theme assets
        Requirements::javascript('themes/mytheme/javascript/map.js');
        Requirements::css('themes/mytheme/css/map.css');
    }
}
