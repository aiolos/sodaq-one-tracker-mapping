<?php
namespace Application\Decoders;

class TrackerPayload extends AbstractDecoder
{
    protected $expectedLength = 21;

    protected function decodePayload()
    {
        $parsed = [];

        $parsed['epoch'] = $this->decodeElement(0, 4);
        $parsed['batvolt'] = $this->decodeElement(4, 1, function ($data) {
            return (3000 + 10 * $data) / 1000;
        });
        $parsed['boardtemp'] = $this->decodeElement(5, 1);
        $parsed['lat'] = $this->decodeElement(6, 4, function ($data) { return $data / 10000000;});
        $parsed['lon'] = $this->decodeElement(10, 4, function ($data) { return $data / 10000000;});
        $parsed['alt'] = $this->decodeElement(14, 2);
        $parsed['speed'] = $this->decodeElement(16, 2);
        $parsed['course'] = $this->decodeElement(18, 1);
        $parsed['numsat'] = $this->decodeElement(19, 1);
        $parsed['fix'] = $this->decodeElement(20, 1);

        $this->decodedPayload = $parsed;
    }
}
