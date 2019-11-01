<?php 
// src/AppBundle/Utils/OutletScraper.php
namespace AppBundle\Utils;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Utils\OutletDetailsParser;

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

		// scrape details for each outlet
		$outletDetails = $crawler->filter('div.single-outlet-post')->each(function ($node) {
			// get outlet name
			$outletName = $node->filter('div.outlet-content div.outlet-title h3')->text();

			// get address
			$address 	= $node->filter('div.outlet-content p.outlet-address')->text();

			$outletDetailsParser 	= new OutletDetailsParser();
			$parsedAddress 			= $outletDetailsParser->parseAddress($address);
			if($parsedAddress === false){
				// we are dealing with an unfamiliar format
				$this->abnormalFormatOutlets[$outletName] = $address;
				return;
			}else{
				$div 	= $node->filter('div');
				$class 	= $div->attr('class');

				// parse certification status
				$certificationStatus = $outletDetailsParser->parseCertificationStatus($class);

				// parse telephone number
				$telephone 		= $outletDetailsParser->parseTelephone($address);

				$outletArray 	= array(
					'outletName' 			=> $outletName,
					'certificationStatus' 	=> $certificationStatus,
					'address' 				=> $parsedAddress,
					'telephoneNumber'		=> $telephone,
					'longitude'				=> null,
					'latitude'				=> null
				);

				// geocode (if needed)
				if($this->needsGeocoding($outletName, $parsedAddress['postcode']) == true){
					$coordinates = $this->geocodeAddress($parsedAddress['propertyNumber'].' '.$parsedAddress['streetName'].', '.$parsedAddress['town'].', '.$parsedAddress['postcode']);

					$outletArray['longitude']		= $coordinates['longitude'];
					$outletArray['latitude']		= $coordinates['latitude'];
				}
				
				$this->outlets[] = $outletArray;

				return $outletArray;
			}			
		});

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
	
		$postcodeTelephoneArray = $this->parsePostcodeAndTelephone($outletAddressArray);

		$postcode 	= $postcodeTelephoneArray[0];
		$telephone 	= $postcodeTelephoneArray[1];
		
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

	/*
	 *
	 * Determines if outlet address needs to be geocoded 
	 * i.e. new outlets or existing outlets with no geo data
 	 *
	 */ 
	private function needsGeocoding($outletName, $postcode)
	{
		// check if outlet exists in db
		$existingOutlet = $this->em->getRepository('AppBundle\Entity\Outlet')->findOneBy(array(
				'outletName' 	=> $outletName,
				'postcode'		=> $postcode,
			));

		if($existingOutlet !==  null){
			return (($existingOutlet->getLongitude() == null || $existingOutlet->getLatitude()) == null) ? true : false;

		}else{
			return true;
		}
	}
}