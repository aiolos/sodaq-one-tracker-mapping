<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gateway_connections")
 */
class GatewayConnection
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

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

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="gatewayConnections")
     */
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="Gateway", inversedBy="gatewayConnections")
     */
    protected $gateway;

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
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param mixed $channel
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return mixed
     */
    public function getSnr()
    {
        return $this->snr;
    }

    /**
     * @param mixed $snr
     */
    public function setSnr($snr)
    {
        $this->snr = $snr;
    }

    /**
     * @return mixed
     */
    public function getRssi()
    {
        return $this->rssi;
    }

    /**
     * @param mixed $rssi
     */
    public function setRssi($rssi)
    {
        $this->rssi = $rssi;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param mixed $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }
}
