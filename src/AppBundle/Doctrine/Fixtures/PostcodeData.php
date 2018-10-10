<?php

// AppBundle/Doctrine/Fixtures/PostcodeData.php
namespace AppBundle\Doctrine\Fixtures;

use Craue\GeoBundle\Doctrine\Fixtures\GeonamesPostalCodeData;
use Doctrine\Common\Persistence\ObjectManager;

class PostcodeData extends GeonamesPostalCodeData {

	public function load(ObjectManager $manager) {
		ini_set('memory_limit', '-1');

		$this->clearPostalCodesTable($manager);
		$this->addEntries($manager, './tmp/GB_full.csv');
	}

}
