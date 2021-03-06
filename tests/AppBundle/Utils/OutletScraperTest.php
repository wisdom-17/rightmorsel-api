<?php

// tests/AppBundle/Util/OutletScraper.php
namespace Tests\AppBundle\Utils;

use AppBundle\Utils\OutletScraper;
use Geocoder\Provider\Provider;
use PHPUnit\Framework\TestCase;
use Geocoder\Model\Coordinates;
use Geocoder\Model\AddressCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class OutletScraperTest extends TestCase
{
    private $outletScraper;
    private $mockGeocoder;
    private $mockEm;

    protected function setUp(): void
    {
        $this->mockGeocoder = $this->createMock(Provider::class);
        $this->mockEm       = $this->createMock(EntityManagerInterface::class);

        $outlet = null;
        // mock the repository so it returns the mock of the outlet
        $outletRepository = $this->createMock(ObjectRepository::class);

        $outletRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($outlet);

        // get the EntityManager to return the mock of the repository
        $this->mockEm->expects($this->any())
            ->method('getRepository')
            ->willReturn($outletRepository);

        $outletScraper = new OutletScraper($this->mockGeocoder, $this->mockEm);

        $this->outletScraper = $outletScraper;
    }

    public function testScrapeOutlets()
    {
    	$outlets 	= $this->outletScraper->scrapeOutlets('http://127.0.0.1:8000/test-webpage/test.html');

    	$this->assertCount(186, $outlets);
    	$this->assertArrayHasKey('outletName', $outlets[0]);
    	$this->assertArrayHasKey('address', $outlets[0]);
    	$this->assertArrayHasKey('telephoneNumber', $outlets[0]);
    }

    /**
     * @dataProvider addressProvider
     */
    // public function testParseAddress($address, $expectedAddress)
    // {
    //     $parsedOutletAddress = $this->outletScraper->parseAddress($address);

    //     $this->assertEquals($expectedAddress, $parsedOutletAddress);
    // }

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