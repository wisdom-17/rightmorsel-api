<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Outlet
 *
 * @ORM\Table(name="outlet")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OutletRepository")
 */
class Outlet
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="outlet_name", type="string", length=255)
     */
    private $outletName;

    /**
     * @var string
     * @ORM\Column(name="building_name", type="string", length=100, nullable=true)
     */
    private $buildingName;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="property_number", type="string", length=20)
     */
    private $propertyNumber;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="street_name", type="string", length=200)
     */
    private $streetName;    

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="area", type="string", length=200)
     */
    private $area;  

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="town", type="string", length=200)
     */
    private $town;  

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="postcode", type="string", length=30)
     */
    private $postcode;

    /**
     * @var string
     * @Assert\NotBlank()
     * @ORM\Column(name="country", type="string", length=200)
     */
    private $country;  

    /**
     * @var string
     *
     * @ORM\Column(name="contact_number", type="string", length=50, nullable=true)
     */
    private $contactNumber;

    /**
     * @var decimal
     *
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8, nullable=true)
     */
    private $longitude;  

    /**
     * @var decimal
     *
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=8, nullable=true)
     */
    private $latitude; 

    /**
     * @var boolean
     * @Assert\NotBlank()
     * @ORM\Column(name="is_active", type="boolean", options={"default" : 0})
     */
    private $isActive; 

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set outletName
     *
     * @param string $outletName
     *
     * @return Outlet
     */
    public function setOutletName($outletName)
    {
        $this->outletName = $outletName;

        return $this;
    }

    /**
     * Get outletName
     *
     * @return string
     */
    public function getOutletName()
    {
        return $this->outletName;
    }

    /**
     * Set postcode
     *
     * @param string $postcode
     *
     * @return Outlet
     */
    public function setPostCode($postcode)
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Get postcode
     *
     * @return string
     */
    public function getPostCode()
    {
        return $this->postcode;
    }

    /**
     * Set buildingName
     *
     * @param string $buildingName
     *
     * @return Outlet
     */
    public function setBuildingName($buildingName)
    {
        $this->buildingName = $buildingName;

        return $this;
    }

    /**
     * Get buildingName
     *
     * @return string
     */
    public function getBuildingName()
    {
        return $this->buildingName;
    }

    /**
     * Set propertyNumber
     *
     * @param string $propertyNumber
     *
     * @return Outlet
     */
    public function setPropertyNumber($propertyNumber)
    {
        $this->propertyNumber = $propertyNumber;

        return $this;
    }

    /**
     * Get propertyNumber
     *
     * @return string
     */
    public function getPropertyNumber()
    {
        return $this->propertyNumber;
    }

    /**
     * Set streetName
     *
     * @param string $streetName
     *
     * @return Outlet
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * Get streetName
     *
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * Set area
     *
     * @param string $area
     *
     * @return Outlet
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return Outlet
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Outlet
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     *
     * @return Outlet
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     *
     * @return Outlet
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Outlet
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set contactNumber
     *
     * @param string $contactNumber
     *
     * @return Outlet
     */
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;

        return $this;
    }

    /**
     * Get contactNumber
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contactNumber;
    }
}
