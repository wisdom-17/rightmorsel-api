<?php 
// src/AppBundle/Utils/OutletDetailsParser.php
namespace AppBundle\Utils;

class OutletDetailsParser
{

    public function parseAddress($outletAddress)
	{
        $outletAddress 			= trim($outletAddress);

        // remove telephone as this is parsed in another method
        if(strpos($outletAddress, 'Tel:') !== false){
            $outletAddress = strstr($outletAddress, 'Tel:', true);
        }

        $outletAddressArray 	= explode(',', $outletAddress);
		$addressFirstLine 		= $outletAddressArray[0];

		// parse property number
		preg_match('/\A\d+[a-zA-Z]?\s?-?\s\d*/', $addressFirstLine, $matches);
		if(count($matches) === 0){
			return false;
		}

		// we are dealing with a consistently formatted address i.e. no building name
		$propertyNumber = trim($matches[0]);
		$streetName 	= trim(substr($addressFirstLine, strpos($addressFirstLine, $propertyNumber)+strlen($propertyNumber)));

		$area 			= trim($outletAddressArray[1]);
		$town			= trim($outletAddressArray[2]);
		$postcode 	    = trim(end($outletAddressArray));

		$parsedOutletAddress = [
			'buildingName'			=> null,
			'propertyNumber' 		=> trim($propertyNumber),
			'streetName'			=> $streetName,
			'area'					=> $area,
			'town'					=> $town,
			'postcode'				=> $postcode
		];

		return $parsedOutletAddress;
    }

    public function parseTelephone($node)
    {

    }
}