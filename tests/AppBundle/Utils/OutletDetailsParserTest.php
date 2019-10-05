<?php

// tests/AppBundle/Util/OutletDetailsParserTest.php
namespace Tests\AppBundle\Utils;

use PHPUnit\Framework\TestCase;
use AppBundle\Utils\OutletDetailsParser;

class OutletDetailsParserTest extends TestCase
{
    private $outletDetailsParser;

    protected function setUp()
    {
        $outletDetailsParser = new OutletDetailsParser();
        $this->outletDetailsParser = $outletDetailsParser;
    }

    /**
     * @dataProvider addressProvider
     */
    public function testParseAddress($address, $expectedAddress)
    {
        $parsedOutletAddress = $this->outletDetailsParser->parseAddress($address);

        $this->assertEquals($expectedAddress, $parsedOutletAddress);
    }

    public function addressProvider()
    {
        return [
            ['
            28 Osborn Street, Whitechapel, London, London, E1 6TD							
            
                                                    Tel: 020 7247 0073	
                
                                    ', 
                [
                    'buildingName'      => null,
                    'propertyNumber'    => '28',
                    'streetName'        => 'Osborn Street',
                    'area'              => 'Whitechapel',
                    'town'              => 'London',
                    'postcode'          => 'E1 6TD',
                ]
            ],
            // ['
            //                 Unit 35, East Shopping Centre, 232 - 236 Green Street, Forest Gate, London, London, E7 8LE   020 3598 5462                      ',
            //     false
            // ],
            ['
            115 New Road, Whitechapel, London, London, E1 1HJ							
                                    ',
                [
                    'buildingName'      => null,
                    'propertyNumber'    => '115',
                    'streetName'        => 'New Road',
                    'area'              => 'Whitechapel',
                    'town'              => 'London',
                    'postcode'          => 'E1 1HJ',
                ]
            ]
        ];
    }
}