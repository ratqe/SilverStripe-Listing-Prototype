<?php

/* this object stores the contact information for an agent, which will be listed on a listing page. */

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;

class ContactObject extends DataObject
{
    private static $db = [
        'ContactName'  => 'Varchar(255)',
        'ContactPhone' => 'Varchar(255)',
        'ContactEmail' => 'Varchar(255)'
    ];

    private static $belongs_many_many = [
        'Listings' => ListingPage::class
    ];

    private static $summary_fields = [
        'ContactName'  => 'Name',
        'ContactPhone' => 'Phone',
        'ContactEmail' => 'Email'
    ];

    private static $searchable_fields = [
        'ContactName'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', TextField::create('ContactName', 'Name of Contact'));
        $fields->addFieldToTab('Root.Main', TextField::create('ContactPhone', 'Phone Number of Contact'));
        $fields->addFieldToTab('Root.Main', TextField::create('ContactEmail', 'Email of Contact'));

        return $fields;
    }

    public function __toString()
    {
        return $this->ContactName ?: 'Unnamed Contact';
    }
}
