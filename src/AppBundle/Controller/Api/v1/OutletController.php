<?php

namespace AppBundle\Controller\Api\v1;

use AppBundle\Entity\Outlet;
use AppBundle\Database\OutletTableWriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class OutletController extends Controller
{
    /**
     * @Route("/api/v1/outlet/new")
     * @Method("POST")
     */
    public function newAction(Request $request, OutletTableWriter $outletTableWriter) 
    {
		$outletName 		= $request->get('outletName');
		$buildingName 		= $request->get('buildingName');
        $propertyNumber 	= $request->get('propertyNumber');
        $streetName 		= $request->get('streetName');
        $area 				= $request->get('area');
        $town 				= $request->get('town');
        $contactNumber 		= $request->get('contactNumber');
        $postcode 			= $request->get('postcode');
        
        $response = $outletTableWriter->insertOutlet($outletName, $buildingName, $propertyNumber, $streetName, $area, $town, $contactNumber, $postcode);

        return $response;
    }

    /**
	 * @Route("/api/v1/outlet/{id}")
	 * @Method("GET")
	 * @param $id
	 */
	public function getAction($id) 
	{
		$outlet = $this->getDoctrine()
		    ->getRepository('AppBundle:Outlet')
		    ->findOneBy(['id' => $id]);

		$data = [
	    	'outletName' 		=> $outlet->getOutletName(),
	    	'buildingName' 		=> $outlet->getBuildingName(),
	    	'propertyNumber' 	=> $outlet->getPropertyNumber(),
	    	'streetName' 		=> $outlet->getStreetName(),
	    	'area' 				=> $outlet->getArea(),
	    	'town' 				=> $outlet->getTown(),
		    'postcode' 			=> $outlet->getPostCode(),
		    'longitude' 		=> $outlet->getLongitude(),
		    'latitude' 			=> $outlet->getLatitude(),
	    	'contactNumber' 	=> $outlet->getContactNumber(),
	    	'isActive' 			=> $outlet->getIsActive()
	  	];

		return new JsonResponse($data);
	}

	/**
	 * @Route("/api/v1/outlets/nearest/{longitude}/{latitude}")
	 * @Method("GET")
	 *
	 * @param $longitude
	 * @param $latitude
	 */
	public function nearestAction($longitude, $latitude) 
	{
		return new JsonResponse([]);
	}

}
