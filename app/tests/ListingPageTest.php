<?php

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ValidationException;

/**
 * Tests for ListingPage geocoding and validation logic.
 */
class ListingPageTest extends SapphireTest
{
    protected static $fixture_file = null; // no fixtures used

    /**
     * Get a mocked ListingPage that overrides geocodeAddress()
     */
    protected function getMockedPage(?array $mockCoords): ListingPage
    {
        $mock = new class extends ListingPage {
            private $mockCoords = null;

            public function setMockCoords(?array $coords)
            {
                $this->mockCoords = $coords;
            }

            // override geocodeAddress to use mock data instead of HTTP request
            protected function geocodeAddress(string $address): ?array
            {
                return $this->mockCoords;
            }
        };

        $mock->setMockCoords($mockCoords);
        return $mock;
    }

    public function testAutoGeocodePopulatesLatLong()
    {
        $mockCoords = ['lat' => -36.848461, 'lng' => 174.763336];
        $page = $this->getMockedPage($mockCoords);
        $page->Address = '1 Queen Street, Auckland';

        $page->write();

        $this->assertEqualsWithDelta($mockCoords['lat'], $page->Latitude, 0.01);
        $this->assertEqualsWithDelta($mockCoords['lng'], $page->Longitude, 0.01);
        $this->assertTrue($page->HasCoords());
    }

    public function testThrowsValidationExceptionForInvalidAddress()
    {
        $this->expectException(ValidationException::class);

        $page = $this->getMockedPage(null);
        $page->Address = 'Some nonsense address that will fail';

        $page->write(); // should throw because geocodeAddress returns null
    }

    public function testThrowsValidationExceptionIfNoAddressOrCoords()
    {
        $this->expectException(ValidationException::class);

        $page = $this->getMockedPage(null);
        $page->Address = ''; // no address
        $page->Latitude = 0;
        $page->Longitude = 0;

        $page->write(); // should throw
    }

    public function testNoGeocodeIfCoordsAlreadySet()
    {
        $page = $this->getMockedPage(null);
        $page->Address = '1 Queen Street, Auckland';
        $page->Latitude = -36.8;
        $page->Longitude = 174.7;

        $page->write(); // should not call geocodeAddress()

        $this->assertEquals(-36.8, $page->Latitude);
        $this->assertEquals(174.7, $page->Longitude);
    }

    public function testGoogleMapsLinkUsesCoordsIfAvailable()
    {
        $page = new ListingPage();
        $page->Latitude = -36.848461;
        $page->Longitude = 174.763336;

        $link = $page->GoogleMapsLink();

        $this->assertStringContainsString('-36.848461', $link);
        $this->assertStringContainsString('174.763336', $link);
    }

    public function testGoogleMapsLinkUsesAddressIfNoCoords()
    {
        $page = new ListingPage();
        $page->Address = '1 Queen Street, Auckland';

        $link = $page->GoogleMapsLink();

        $this->assertStringContainsString(urlencode('1 Queen Street, Auckland'), $link);
    }

    public function testHasCoordsReturnsFalseForZeroZero()
    {
        $page = new ListingPage();
        $page->Latitude = 0.0;
        $page->Longitude = 0.0;

        $this->assertFalse($page->HasCoords());
    }
}
