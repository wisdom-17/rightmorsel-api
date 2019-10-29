<?php

namespace AppBundle\Database;

use AppBundle\Entity\Outlet;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class OutletTableWriter
{
	private $validator;

	private $entityManager;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $this->validator 	= $validator;
        $this->em 			= $em;

    }

	// inserts outlet to db
	public function insertOutlet($outletName, $buildingName = null, $propertyNumber, $streetName, $area, $town, $contactNumber, $postcode, $longitude = null, $latitude = null, $certificationStatus)
	{

		// check if outlet exists already
		$existingOutlet = $this->em->getRepository('AppBundle\Entity\Outlet')->findOneBy(array(
				'outletName' 	=> $outletName,
				'postcode'		=> $postcode
			)
		);

		$operationType = $this->determineOperationType($existingOutlet, $certificationStatus);

		if($operationType == 'certification_revoked'){
			$existingOutlet->setIsActive(false);

			$this->em->persist($existingOutlet);
			$this->em->flush(); // update

			$response = new Response('', 200, array('content-type' => 'text/html'));
			$response->setContent('Deactivated, revoked certification.');
		}elseif($operationType == 'geodata_needs_updating'){
			if($longitude !== null){
				$existingOutlet->setLongitude($longitude);
			}
			if($latitude !== null){
				$existingOutlet->setLatitude($latitude);	
			}

			$this->em->persist($existingOutlet);
			$this->em->flush(); // update

			$response = new Response('', 200, array('content-type' => 'text/html'));
			$response->setContent('Geodata updated.');
		}elseif($operationType == 'existing_outlet_no_changes'){
			$response = new Response('', 422, array('content-type' => 'text/html'));
        	$response->setContent('Outlet exists.');
		}elseif($operationType == 'new_outlet'){		
			$outlet = new Outlet();
			$outlet->setOutletName($outletName);
			$outlet->setBuildingName($buildingName);
			$outlet->setPropertyNumber($propertyNumber);
			$outlet->setStreetName($streetName);
			$outlet->setArea($area);
			$outlet->setTown($town);
			$outlet->setContactNumber($contactNumber);
			$outlet->setPostCode($postcode);
			$outlet->setCountry('England');
			$outlet->setIsActive(0);

			if($longitude !== null){
				$outlet->setLongitude($longitude);
			}
			if($latitude !== null){
				$outlet->setLatitude($latitude);	
			}
	
			// validate constraints
	    	$errors = $this->validator->validate($outlet);
	    	if (count($errors) > 0) {
				$response = new Response('Validation failed: ', 422, array('content-type' => 'text/html'));

		        $errorsString = (string) $errors;
		        $response->setContent($errorsString);
		    }else{
				$this->em->persist($outlet);
				$this->em->flush(); // insert

		    	$response = new Response('Outlet #'.$outlet->getId().' has been successfully saved.', 201);
		    }			
		}

		return $response;
	}

	private function determineOperationType($existingOutlet, $certificationStatus){
		if($existingOutlet !== null){
			if($existingOutlet->getIsActive() == true && $certificationStatus == 'revoked'){
				$operationType = 'certification_revoked';
				return $operationType;
			}

			if($existingOutlet->getLongitude() == null || $existingOutlet->getLatitude() == null){
				$operationType = 'geodata_needs_updating';
			}else{
				$operationType = 'existing_outlet_no_changes';
			}
		}else{
			$operationType = 'new_outlet';
		}
		return $operationType;
	}
}