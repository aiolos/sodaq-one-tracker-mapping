<?php
namespace Application\Decoders;

class BasicTrackerPayload extends AbstractDecoder
{
    protected $expectedLength = 21;

    protected function decodePayload()
    {
        $parsed = [];
        $hex = $this->hexPayload;

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

        $this->decodedPayload = $parsed;
    }
}
