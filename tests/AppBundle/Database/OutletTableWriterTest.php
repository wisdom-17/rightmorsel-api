<?php

namespace Tests\AppBundle\Database;

use AppBundle\Entity\Outlet;
use AppBundle\Database\OutletTableWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;

class OutletTableWriterTest extends TestCase
{

    private $validator;

    private $mockEm;

    public function __construct()
    {
        $this->validator    = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $this->mockEm       = $this->createMock(EntityManagerInterface::class);
    }

    public function testSuccessfulInsertOutlet()
    {
        $outlet = null;
        // mock the repository so it returns the mock of the outlet
        $outletRepository = $this->createMock(ObjectRepository::class);

        $outletRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($outlet);

        // get the EntityManager to return the mock of the repository
        $this->mockEm->expects($this->any())
            ->method('getRepository')
            ->willReturn($outletRepository);

        $outletTableWriter  = new OutletTableWriter($this->validator, $this->mockEm);
        $response           = $outletTableWriter->insertOutlet(
            'Test Outlet', null, 32, 'Dawn Crescent', 'Stratford', 'London', null, 'E15 3NT'
        );

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testUnsuccessfulInsertOutlet()
    {
        $outlet = null;
        // mock the repository so it returns the mock of the outlet
        $outletRepository = $this->createMock(ObjectRepository::class);

        $outletRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($outlet);

        // get the EntityManager to return the mock of the repository
        $this->mockEm->expects($this->any())
            ->method('getRepository')
            ->willReturn($outletRepository);

        $outletTableWriter  = new OutletTableWriter($this->validator, $this->mockEm);
        $response           = $outletTableWriter->insertOutlet(
            '','', '', '', '', '', '', 'EXX 1XX'
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testExistingOuletInsert()
    {
        $outlet = new Outlet();
        $outlet->setOutletName('Test Outlet');
        $outlet->setPostCode('E12 1AB');

        // mock the repository so it returns the mock of the outlet
        $outletRepository = $this->createMock(ObjectRepository::class);

        $outletRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($outlet);

        // get the EntityManager to return the mock of the repository
        $this->mockEm->expects($this->any())
            ->method('getRepository')
            ->willReturn($outletRepository);

        $outletTableWriter  = new OutletTableWriter($this->validator, $this->mockEm);
        $response           = $outletTableWriter->insertOutlet(
            'Test Outlet', null, '1', 'Test', 'Test', 'Test', null, 'E12 1AB'
        );

        $this->assertEquals(422, $response->getStatusCode());
    }
}