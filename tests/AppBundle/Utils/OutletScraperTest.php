<?php

// tests/AppBundle/Util/OutletScraper.php
namespace Tests\AppBundle\Utils;

use AppBundle\Utils\OutletScraper;
use Geocoder\Provider\Provider;
use PHPUnit\Framework\TestCase;

class OutletScraperTest extends TestCase
{
    public function testScrapeOutlets()
    {
    	$scraper 	= new OutletScraper('http://127.0.0.1:8000/test-webpage/test.html', new Provider);

    	$outlets 	= $scraper->scrapeOutlets();

    	$this->assertCount(210, $outlets);
    	$this->assertArrayHasKey('outletName', $outlets[0]);
    	$this->assertArrayHasKey('outletAddress', $outlets[0]);
    }

    /**
     * @dataProvider addressProvider
     */
    public function testParseAddress($address, $expectedAddress)
    {

        $scraper             = new OutletScraper('http://127.0.0.1:8000/test-webpage/test.html', new Provider);
        $parsedOutletAddress = $scraper->parseAddress($address);

        $this->assertEquals($expectedAddress, $parsedOutletAddress);
    }

	public function testGeocodeAddress()
    {
        $scraper = new OutletScraper('http://127.0.0.1:8000/test-webpage/test.html', new Provider);

        $geocodedAddress = $scraper->geocodeAddress('770 London Road, Thornton Heath, London, CR7 6JB');

        $this->assertEquals(array('lat' => '51.3946472', 'lon' => '-0.1143172'), $geocodedAddress);

    }

    public function addressProvider()
    {
        return [
            ['
                            770 London Road, Thornton Heath, London, London, CR7 6JB   020 8683 1767    ', 
                [
                    'buildingName'      => null,
                    'propertyNumber'    => '770',
                    'streetName'        => 'London Road',
                    'area'              => 'Thornton Heath',
                    'town'              => 'London',
                    'postcode'          => 'CR7 6JB',
                    'contactNumber'     => '020 8683 1767'
                ]
            ],
            ['
                            Unit 35, East Shopping Centre, 232 - 236 Green Street, Forest Gate, London, London, E7 8LE   020 3598 5462                      ',
                false
            ],
            ['
                            755 High Road, Leytonstone, London, London, E11 4QS   -                     ',
                [
                    'buildingName'      => null,
                    'propertyNumber'    => '755',
                    'streetName'        => 'High Road',
                    'area'              => 'Leytonstone',
                    'town'              => 'London',
                    'postcode'          => 'E11 4QS',
                    'contactNumber'     => null
                ]
            ]
        ];
    }
}