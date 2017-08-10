<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gateway_connections")
 */
class GatewayConnections
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
     * @ORM\Column(name="message_id")
     */
    protected $messageId;

    /**
     * @ORM\Column(name="channel")
     */
    protected $channel;

    /**
     * @ORM\Column(name="snr")
     */
    protected $snr;

    /**
     * @ORM\Column(name="rssi")
     */
    protected $rssi;
}
