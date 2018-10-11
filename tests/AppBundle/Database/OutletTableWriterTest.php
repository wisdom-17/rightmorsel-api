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
            'Test Outlet', null, 32, 'Dawn Crescent', 'Stratford', 'London', null, 'E15 3NT', null, null, 'certified'
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
            '','', '', '', '', '', '', 'EXX 1XX', null, null, ''
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testExistingOutletInsert()
    {
        $outlet = new Outlet();
        $outlet->setOutletName('Test Outlet');
        $outlet->setPostCode('E12 5AB');
        $outlet->setLatitude('51.551258');
        $outlet->setLongitude('0.045479');
        $outlet->setIsActive(1);

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
            'Test Outlet', null, '1', 'Test', 'Test', 'Test', null, 'E12 1AB', null, null, 'certified'
        );

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function testUpdateExistingOutletCertification()
    {
        $outlet = new Outlet();
        $outlet->setOutletName('Test Outlet');
        $outlet->setPostCode('E12 1AB');
        $outlet->setIsActive(1);

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
            'Test Outlet', null, '1', 'Test', 'Test', 'Test', null, 'E12 1AB', null, null, 'revoked'
        );

        $this->assertEquals(200, $response->getStatusCode());
    }
}