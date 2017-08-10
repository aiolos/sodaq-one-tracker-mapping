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
}
