<?php 
// src/AppBundle/Utils/OutletScraper.php
namespace AppBundle\Utils;

use Goutte\Client;

class OutletScraper
{
	private $url;
	public $outlets;
	public $abnormalFormatOutlets;

	public function __construct($url = null)
	{
		$this->url 						= $url;
		$this->outlets 					= [];
		$this->abnormalFormatOutlets 	= [];
	}

	// returns an array of outlets and their details
	public function scrapeOutlets()
	{
		$goutteClient = new Client();

		$crawler = $goutteClient->request('GET', $this->url);
		$crawler = $crawler->filterXPath('//div[@id="outlet_items"]/*');

		// scrape names
		$outletNames = $crawler->filter('article')->each(function ($node) {
			return $node->filter('div.outlet-content div.outlet-title h3')->text();
		});

		// scrape address
		$outletAddresses = $crawler->filter('article')->each(function ($node) {
			return $node->filter('div.outlet-content p.outlet-address')->text();
		});

		foreach ($outletNames as $key => $outletName) {
			$outletDetails 					= [];
			$formattedAddress				= $this->parseAddress($outletAddresses[$key]);

			if($formattedAddress == false){
				$this->abnormalFormatOutlets[$outletName] = $outletAddresses[$key];
			}else{
				$outletDetails['outletName'] 	= $outletName;
				$outletDetails['outletAddress']	= $formattedAddress;
				$this->outlets[] 			= $outletDetails;
			}
		}

		return $this->outlets;
	}
	
	private function parseAddress($outletAddress)
	{
		$outletAddress 			= trim($outletAddress);
		$outletAddressArray 	= explode(',', $outletAddress);
		$addressFirstLine 		= explode(' ', $outletAddressArray[0]); // extract building name, number and street name

		 // identify whether address starts with a building name or property number
		$addressFirstPart 		= array_shift($addressFirstLine);
		$buildingName			= null;

		if(is_numeric($addressFirstPart[0])){ // check if it begins with a number 
			// we are dealing with a simple and straightforward address i.e. no building name
			$propertyNumber = $addressFirstPart;
			$streetName 	= implode(' ', $addressFirstLine); // join the remaining elements
			$area 			= trim($outletAddressArray[1]); // extract area
			$town			= trim($outletAddressArray[2]); //extract town
		}else{ // address format does not follow the same format so will have to be manually entered later
			return false;
		}
	
		$postcodeTelephoneArray = explode('   ',end($outletAddressArray));
		$postcode 				= trim($postcodeTelephoneArray[0]);		// extract postcode
		$telephone 				= trim($postcodeTelephoneArray[1]);		// extract telephone
		$telephone 				= ($telephone == '-') ? null : $telephone;

		$parsedOutletAddress = [
			'buildingName'			=> $buildingName,
			'propertyNumber' 		=> $propertyNumber,
			'streetName'			=> $streetName,
			'area'					=> $area,
			'town'					=> $town,
			'postcode'				=> $postcode,
			'contactNumber'			=> $telephone
		];

		return $parsedOutletAddress;
	}

	private function geocodePostcode()
	{

	}
}