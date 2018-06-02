<?php

namespace Tests\AppBundle\Database;

use AppBundle\Database\OutletTableWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Doctrine\ORM\EntityManagerInterface;

class OutletTableWriterTest extends TestCase
{

    private $validator;

    private $mockEm;

    public function __construct()
    {
        $this->validator    = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $this->mockEm       = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()->getMock();
    }

    public function testSuccessfulInsertOutlet()
    {
        $outletTableWriter  = new OutletTableWriter($this->validator, $this->mockEm);
        $response           = $outletTableWriter->insertOutlet(
            'Test Outlet', null, 32, 'Dawn Crescent', 'Stratford', 'London', null, 'E15 3NT'
        );

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testUnsuccessfulInsertOutlet()
    {
        $outletTableWriter  = new OutletTableWriter($this->validator, $this->mockEm);
        $response           = $outletTableWriter->insertOutlet(
            '','', '', '', '', '', '', 'EXX 1XX'
        );

        $this->assertEquals(422, $response->getStatusCode());
    }
}