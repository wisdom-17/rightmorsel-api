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

    /**
     * @dataProvider telephoneProvider
     */
    public function testParseTelephone($outletDetails, $expectedTelephoneNumber)
    {
        $parsedTelephoneNumber = $this->outletDetailsParser->parseTelephone($outletDetails);

        $this->assertEquals($expectedTelephoneNumber, $parsedTelephoneNumber);
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
            ['
							Kiosk 32, Unit C 399 Edgware Road, Colindale, London, London, NW9 0FH					
							
                            Tel: 07438 935891	

            ',
                false
            ],
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

    public function telephoneProvider()
    {
        return [
            ['
            28 Osborn Street, Whitechapel, London, London, E1 6TD							
            
                                                    Tel: 020 7247 0073	
                
                                    ', 
                
            '020 7247 0073'
                
            ],
            ['
							Kiosk 32, Unit C 399 Edgware Road, Colindale, London, London, NW9 0FH					
							
                            Tel: 07438 935891	

            ',
                '07438 935891'
            ],
            ['
            115 New Road, Whitechapel, London, London, E1 1HJ							
                                    ',
                
                null
                
            ]
        ];
    }
}