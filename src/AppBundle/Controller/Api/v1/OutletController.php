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
     * @Route("/api/v1/outlet")
     * @Method("POST")
     */
    public function newAction(Request $request, OutletTableWriter $OutletTableWriter) 
    {
		$outletName 		= $request->get('outletName');
		$buildingName 		= $request->get('buildingName');
        $propertyNumber 	= $request->get('propertyNumber');
        $streetName 		= $request->get('streetName');
        $area 				= $request->get('area');
        $town 				= $request->get('town');
        $contactNumber 		= $request->get('contactNumber');
        $postcode 			= $request->get('postcode');
        
        $response = $OutletTableWriter->insertOutlet($outletName, null, $propertyNumber, $streetName, $area, $town, $contactNumber, $postcode);

        return $response;
        // var_dump(get_class($outlet));die;

		// $outlet = new Outlet();
		// $outlet->setOutletName($outletName);
		// $outlet->setBuildingName($buildingName);
		// $outlet->setPropertyNumber($propertyNumber);
		// $outlet->setStreetName($streetName);
		// $outlet->setArea($area);
		// $outlet->setTown($town);
		// $outlet->setContactNumber($contactNumber);
		// $outlet->setPostCode($postcode);
		// $outlet->setIsActive(0);

  // 		$validator = $this->get('validator'); // validate constraints
  //   	$errors = $validator->validate($outlet);
  //   	if (count($errors) > 0) {
		// 	$response = new Response('', 422, array('content-type' => 'text/html'));

	 //        $errorsString = (string) $errors;
	 //        $response->setContent($errorsString);
	 //        return $response;
	 //    }



		// $em = $this->getDoctrine()->getManager();
		// $em->persist($outlet);
		// $em->flush(); // save
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
	    	'outlet_name' => $outlet->getOutletName(),
		    'post_code' => $outlet->getPostCode(),
	  	];

		return new JsonResponse($data);
	}
}
