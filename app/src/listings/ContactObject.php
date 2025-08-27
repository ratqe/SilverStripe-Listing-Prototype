<?php

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;

class ContactObject extends DataObject {
    private static $db = [
        // contact
        'ContactName' => 'Varchar(255)',
        'ContactPhone' => 'Varchar(255)',
        'ContactEmail' => 'Varchar(255)'
    ];

    // field code for contact
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', TextField::create('ContactName', 'Name of Contact'));
        $fields->addFieldToTab('Root.Main', TextField::create('ContactPhone', 'Phone Number of Contact'));
        $fields->addFieldToTab('Root.Main', TextField::create('ContactEmail', 'Email of Contact'));

        return $fields;
    }
}