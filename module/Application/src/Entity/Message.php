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

    /**
     * Decode the raw payload in the message,
     * see http://forum.sodaq.com/t/interpreting-payload-data-from-the-sodaqone-universaltracker/374
     * for more information
     * @param $rawPayload
     * @return array
     */
    public static function decodeRawPayload($rawPayload)
    {
        $parsed = [];
        $hex = bin2hex(base64_decode($rawPayload));
        // Currently we only support the sodaq one tracker payload, which sends only 21 bytes
        if (strlen($hex) !== 42) {
            return null;
        }

        $parsed['epoch'] = hexdec(substr($hex, 6, 2) . substr($hex, 4, 2) . substr($hex, 2, 2) . substr($hex, 0, 2));
        $parsed['batvolt'] = (3000 + 10 * hexdec(substr($hex, 8, 2))) / 1000;
        $parsed['boardtemp'] = hexdec(substr($hex, 10, 2));
        $parsed['lat'] = hexdec(substr($hex, 18, 2) . substr($hex, 16, 2) . substr($hex, 14, 2) . substr($hex, 12, 2)) / 10000000;
        $parsed['lon'] = hexdec(substr($hex, 26, 2) . substr($hex, 24, 2) . substr($hex, 22, 2) . substr($hex, 20, 2)) / 10000000;
        $parsed['alt'] = hexdec(substr($hex, 30, 2) . substr($hex, 28, 2));
        $parsed['speed'] = hexdec(substr($hex, 34, 2) . substr($hex, 32, 2));
        $parsed['course'] = hexdec(substr($hex, 36, 2));
        $parsed['numsat'] = hexdec(substr($hex, 38, 2));
        $parsed['fix'] = hexdec(substr($hex, 40, 2));

        return $parsed;
    }
}
