<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gateways")
 */
class Gateway
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="gtw_id")
     */
    protected $gtwId;

    /**
     * @ORM\Column(name="latitude")
     */
    protected $latitude;

    /**
     * @ORM\Column(name="longitude")
     */
    protected $longitude;

    /**
     * @ORM\Column(name="altitude")
     */
    protected $altitude;

    /**
     * @ORM\OneToMany(targetEntity="GatewayConnection", mappedBy="gateway")
     */
    protected $gatewayConnections;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getGtwId()
    {
        return $this->gtwId;
    }

    /**
     * @param mixed $gtwId
     */
    public function setGtwId($gtwId)
    {
        $this->gtwId = $gtwId;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return mixed
     */
    public function getAltitude()
    {
        return $this->altitude;
    }

    /**
     * @param mixed $altitude
     */
    public function setAltitude($altitude)
    {
        $this->altitude = $altitude;
    }

    /**
     * @return mixed
     */
    public function getGatewayConnections()
    {
        return $this->gatewayConnections;
    }

    /**
     * @param mixed $gatewayConnections
     */
    public function setGatewayConnections($gatewayConnections)
    {
        $this->gatewayConnections = $gatewayConnections;
    }
}
