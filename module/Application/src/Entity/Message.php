<?php

namespace Application\Entity;

use Application\Decoders\BasicTrackerPayload;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="messages")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="application_id")
     */
    protected $appId;

    /**
     * @ORM\Column(name="device_id")
     */
    protected $deviceId;

    /**
     * @ORM\Column(name="hardware_serial")
     */
    protected $hardwareSerial;

    /**
     * @ORM\Column(name="port", type="integer")
     */
    protected $port;

    /**
     * @ORM\Column(name="counter", type="integer")
     */
    protected $counter;

    /**
     * @ORM\Column(name="raw_payload", type="string")
     */
    protected $rawPayload;

    /**
     * @ORM\Column(name="decoded_payload", type="text")
     */
    protected $decodedPayload;

    /**
     * @ORM\Column(name="metadata", type="text")
     */
    protected $metadata;

    /**
     * @ORM\Column(name="downlink_url", type="string")
     */
    protected $downlinkUrl;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return Message
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     *
     * @return Message
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }

    /**
     * @param mixed $deviceId
     *
     * @return Message
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHardwareSerial()
    {
        return $this->hardwareSerial;
    }

    /**
     * @param mixed $hardwareSerial
     *
     * @return Message
     */
    public function setHardwareSerial($hardwareSerial)
    {
        $this->hardwareSerial = $hardwareSerial;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     *
     * @return Message
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @param mixed $counter
     *
     * @return Message
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawPayload()
    {
        return $this->rawPayload;
    }

    /**
     * @param mixed $rawPayload
     *
     * @return Message
     */
    public function setRawPayload($rawPayload)
    {
        $this->rawPayload = $rawPayload;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param mixed $metadata
     *
     * @return Message
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDownlinkUrl()
    {
        return $this->downlinkUrl;
    }

    /**
     * @param mixed $downlinkUrl
     *
     * @return Message
     */
    public function setDownlinkUrl($downlinkUrl)
    {
        $this->downlinkUrl = $downlinkUrl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecodedPayload()
    {
        return $this->decodedPayload;
    }

    /**
     * @param mixed $decodedPayload
     */
    public function setDecodedPayload($decodedPayload)
    {
        $this->decodedPayload = $decodedPayload;
    }

    /**
     * @param Payload $payload
     *
     * @return Message
     */
    public static function createFromPayload($payload)
    {
        $message = new Message();
        $content = json_decode($payload->getContent(), true);

        $message
            ->setId($payload->getId())
            ->setAppId($content['app_id'])
            ->setDeviceId($content['dev_id'])
            ->setHardwareSerial($content['hardware_serial'])
            ->setPort($content['port'])
            ->setCounter($content['counter'])
            ->setRawPayload($content['payload_raw'])

            ->setMetadata(json_encode($content['metadata']))
            ->setDownlinkUrl($content['downlink_url'])
        ;
        try {
            $decoder = new BasicTrackerPayload($message->getRawPayload());
            $message->setDecodedPayload(json_encode($decoder->getDecodedPayload()));
        } catch (\Exception $e) {
            // For now, we silently ignore
        }

        return $message;
    }
}
