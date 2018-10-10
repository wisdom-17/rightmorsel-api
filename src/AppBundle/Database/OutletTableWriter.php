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
				'postCode'		=> $postcode
			)
		);

		if($existingOutlet !== null){
			// check existing outlet's isActive against certification status
			if($existingOutlet->getIsActive() == true && $certificationStatus == 'revoked'){
				$existingOutlet->setIsActive(false);

				$this->em->persist($existingOutlet);
				$this->em->flush(); 

				$response = new Response('', 200, array('content-type' => 'text/html'));
				$response->setContent('Outlet certification revoked, so deactivated outlet.');

				return $response;
			}elseif($existingOutlet->getLongitude() == null || $existingOutlet->getLatitude() == null){
				$outlet = $existingOutlet;
			}else{
				$response = new Response('', 422, array('content-type' => 'text/html'));
	        	$response->setContent('Outlet already exists');

	        	return $response;
			}
		}else{
			$outlet = new Outlet();
			$outlet->setOutletName($outletName);
			$outlet->setBuildingName($buildingName);
			$outlet->setPropertyNumber($propertyNumber);
			$outlet->setStreetName($streetName);
			$outlet->setArea($area);
			$outlet->setTown($town);
			$outlet->setContactNumber($contactNumber);
			$outlet->setPostCode($postcode);

		}
		
		if($longitude !== null){
			$outlet->setLongitude($longitude);
		}
		if($latitude !== null){
			$outlet->setLatitude($latitude);	
		}
	
		$outlet->setIsActive(0);

  		// $validator = $this->get('validator'); // validate constraints
    	$errors = $this->validator->validate($outlet);
    	if (count($errors) > 0) {
			$response = new Response('Validation failed: ', 422, array('content-type' => 'text/html'));

	        $errorsString = (string) $errors;
	        $response->setContent($errorsString);
	        return $response;
	    }

		$this->em->persist($outlet);
		$this->em->flush(); // save

 		return new Response('Outlet #'.$outlet->getId().' has been successfully saved.', 201);
	}
}