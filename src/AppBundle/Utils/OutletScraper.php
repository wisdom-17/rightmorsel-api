<?php 
// src/AppBundle/Utils/OutletScraper.php
namespace AppBundle\Utils;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

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

        $goutteClient->setClient(new GuzzleClient(array(
            // disable SSL certificate check
            'verify' => false
        )));

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
				// check that outlet does not exist
					// geo code outlet

				$outletDetails['outletName'] 	= $outletName;
				$outletDetails['outletAddress']	= $formattedAddress;
				$this->outlets[] 			    = $outletDetails;
			}
		}

		return $this->outlets;
	}
	
	public function parseAddress($outletAddress)
	{
		$outletAddress 			= trim($outletAddress);
		$outletAddressArray 	= explode(',', $outletAddress);
		$addressFirstLine 		= $outletAddressArray[0];

		// parse property number
		preg_match('/\A\d+[a-zA-Z]?\s?-?\s\d*/', $addressFirstLine, $matches);

		if(count($matches) === 0){
			return false;
		}

		// we are dealing with a simple and straightforward address i.e. no building name
		$propertyNumber = trim($matches[0]);
		$streetName 	= trim(substr($addressFirstLine, strpos($addressFirstLine, $propertyNumber)+strlen($propertyNumber)));

		$area 			= trim($outletAddressArray[1]); // extract area
		$town			= trim($outletAddressArray[2]); //extract town
	
		$postcodeTelephoneArray = explode('   ',end($outletAddressArray));
		$postcode 				= trim($postcodeTelephoneArray[0]);		// extract postcode
		$telephone 				= trim($postcodeTelephoneArray[1]);		// extract telephone
		$telephone 				= ($telephone == '-') ? null : $telephone;

		$parsedOutletAddress = [
			'buildingName'			=> null,
			'propertyNumber' 		=> trim($propertyNumber),
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