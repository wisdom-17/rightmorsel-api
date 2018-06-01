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

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em/*, Response $response*/)
    {
        $this->validator 	= $validator;
        $this->em 			= $em;
        // $this->response 	= $response;

    }

	// inserts outlet to db
	public function insertOutlet($outletName, $buildingName = null, $propertyNumber, $streetName, $area, $town, $contactNumber, $postcode)
	{
		$outlet = new Outlet();
		$outlet->setOutletName($outletName);
		$outlet->setBuildingName($buildingName);
		$outlet->setPropertyNumber($propertyNumber);
		$outlet->setStreetName($streetName);
		$outlet->setArea($area);
		$outlet->setTown($town);
		$outlet->setContactNumber($contactNumber);
		$outlet->setPostCode($postcode);
		$outlet->setIsActive(0);

  		// $validator = $this->get('validator'); // validate constraints
    	$errors = $this->validator->validate($outlet);
    	if (count($errors) > 0) {
			$response = new Response('', 422, array('content-type' => 'text/html'));

	        $errorsString = (string) $errors;
	        $response->setContent($errorsString);
	        return $response;
	    }

		$this->em->persist($outlet);
		$this->em->flush(); // save

 		return new Response('Outlet #'.$outlet->getId().' has been successfully saved.', 201);
	}
}