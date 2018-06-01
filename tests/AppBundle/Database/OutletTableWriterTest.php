<?php

namespace Tests\AppBundle\Database;

use AppBundle\Database\OutletTableWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class OutletTableWriterTest extends TestCase
{
    public function testSuccessfulInsertOutlet()
    {

        $mockValidator  = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockEm         = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()->getMock();


        $outletTableWriter  = new OutletTableWriter($mockValidator, $mockEm);
        $response           = $outletTableWriter->insertOutlet(
            'Test Outlet', null, 32, 'Dawn Crescent', 'Stratford', 'London', null, 'E15 3NT'
        );

        $this->assertEquals(201, $response->getStatusCode());


    }

    public function testUnsuccessfulInsertOutlet()
    {

    }
}