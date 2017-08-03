<?php
namespace Application\Decoders;

use Application\Exceptions\InvalidPayloadException;

abstract class AbstractDecoder
{
    protected $rawPayload;
    protected $hexPayload;

    protected $decodedPayload;

    /**
     * Expected length of the payload in bytes
     * @var int
     */
    protected $expectedLength = 0;

    protected $swapEndianness;

    public function __construct($rawPayload, $swapEndianness = true)
    {
        if (!is_string($rawPayload)
            || empty($rawPayload)
        ) {
            throw new InvalidPayloadException('Empty payload given');
        }
        $this->rawPayload = $rawPayload;
        $hex = bin2hex(base64_decode($rawPayload));
        // Hexadecimal is 2 character per byte
        if ((strlen($hex) / 2) !== $this->expectedLength) {
            throw new InvalidPayloadException('Payload not ' . $this->expectedLength . ' bytes');
        }

        $this->hexPayload = $hex;
        $this->swapEndianness = $swapEndianness;
    }

    public function getDecodedPayload()
    {
        if (is_null($this->decodedPayload)) {
            $this->decodePayload();
        }
        return $this->decodedPayload;
    }

    abstract protected function decodePayload();

    protected function decodeElement($startByte, $numberOfBytes, $callback = null)
    {
        $result = '';

        for ($i = $startByte * 2; $i < ($startByte + $numberOfBytes) * 2; $i += 2) {
            $newCharacter = substr($this->hexPayload, $i, 2);
            $result = $this->swapEndianness ? $newCharacter . $result : $result . $newCharacter;
        }

        if (is_null($callback)) {
            return hexdec($result);
        }
        return call_user_func($callback, hexdec($result));
    }
}
