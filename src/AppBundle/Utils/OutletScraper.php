<?php 
// src/AppBundle/Utils/OutletScraper.php
namespace AppBundle\Utils;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Doctrine\ORM\EntityManagerInterface;

class OutletScraper
{
	private $geocoder;
	private $em;
	public $outlets;
	public $abnormalFormatOutlets;

	public function __construct(Provider $geocoder, EntityManagerInterface $em)
	{
		$this->geocoder					= $geocoder;
		$this->em 						= $em;
		$this->outlets 					= [];
		$this->abnormalFormatOutlets 	= [];
	}

	// returns an array of outlets and their details
	public function scrapeOutlets($url = '')
	{
		$goutteClient = new Client();

        $goutteClient->setClient(new GuzzleClient(array(
            // disable SSL certificate check
            'verify' => false
        )));

		$crawler = $goutteClient->request('GET', $url);
		$crawler = $crawler->filterXPath('//div[@id="outlet_items"]/*');

		// identify if certified or revoked
		$certificationStatus = $crawler->filter('div.single-outlet-post')->each(function ($node) {
			$div 	= $node->filter('div');
			$class 	= $div->attr('class');

			$lastClass = $this->getCertificationStatus($class);

			return $lastClass;
		});

		// scrape names
		$outletNames = $crawler->filter('article')->each(function ($node) {
			return $node->filter('div.outlet-content div.outlet-title h3')->text();
		});

		// scrape address
		$outletAddresses = $crawler->filter('article')->each(function ($node) {
			return $node->filter('div.outlet-content p.outlet-address')->text();
		});

		foreach ($outletNames as $key => $outletName) {
			$outletDetails 							= [];
			$outletDetails['certificationStatus'] 	= $certificationStatus[$key];
			$outletDetails['longitude']				= null;
			$outletDetails['latitude']				= null;
			
			$formattedAddress				= $this->parseAddress($outletAddresses[$key]);

			if($formattedAddress == false){
				$this->abnormalFormatOutlets[$outletName] = $outletAddresses[$key];
			}else{
				// check that outlet does not exist in our db
				$existingOutlet = $this->em->getRepository('AppBundle\Entity\Outlet')->findOneBy(array(
						'outletName' 	=> $outletName,
						'postCode'		=> $formattedAddress['postcode'],
					));

				$outletExists 	= $existingOutlet !== null ? true : false;
				$needsGeocoding = false;		
				if($outletExists === true){
					// check if there valid lat and lon data for outlet (if it exists)
					$needsGeocoding = ($existingOutlet->getLongitude() == null || $existingOutlet->getLatitude() == null) ? true : false;
				}

				$address = $formattedAddress['propertyNumber'].' '.$formattedAddress['streetName'].', '.$formattedAddress['town'].', '.$formattedAddress['postcode'];	

				// geocode (if needed)
				if($needsGeocoding == true || $outletExists === false){
					$coordinates = $this->geocodeAddress($address);

					$outletDetails['longitude']		= $coordinates['longitude'];
					$outletDetails['latitude']		= $coordinates['latitude'];
				}

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

	private function getCertificationStatus($divClass)
	{
		$pieces 	= explode(' ', $divClass);
		$lastClass 	= array_pop($pieces);

		return $lastClass;
	}

	public function geocodeAddress($address)
	{
		$coordinates 		= ['longitude' => null, 'latitude' => null];
		$addressCollection 	= $this->geocoder->geocodeQuery(GeocodeQuery::create($address));

		if($addressCollection->count() > 0){
			$address 				= $addressCollection->first();
			$coordinatesResultArray = $address->getCoordinates()->toArray();
			
			$coordinates['longitude'] 		= $coordinatesResultArray[0];	
			$coordinates['latitude'] 		= $coordinatesResultArray[1];	
		}
		
		return $coordinates;
	}
}