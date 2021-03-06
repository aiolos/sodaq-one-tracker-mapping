<?php

namespace ApplicationTest\Decoders;

use Application\Decoders\BasicTrackerPayload;
use ApplicationTest\AbstractTestCase;

class BasicTrackerPayloadTest extends AbstractTestCase
{
    public function testPayloadCanBeDecoded()
    {
        $decoder = new BasicTrackerPayload("WTSDWXgcsbw3H5hD6gIVAAEAyAD/");
        $decoderResult = $decoder->getDecodedPayload();

        $this->assertEquals(1501770841, $decoderResult['epoch']);
        $this->assertEquals(4.2, $decoderResult['batvolt']);
        $this->assertEquals(28, $decoderResult['boardtemp']);
        $this->assertEquals(52.3746481, $decoderResult['lat']);
        $this->assertEquals(4.890716, $decoderResult['lon']);
        $this->assertEquals(21, $decoderResult['alt']);
        $this->assertEquals(1, $decoderResult['speed']);
        $this->assertEquals(200, $decoderResult['course']);
        $this->assertEquals(0, $decoderResult['numsat']);
        $this->assertEquals(255, $decoderResult['fix']);
    }

    /**
     * @expectedException \Application\Exceptions\InvalidPayloadException
     * @expectedExceptionMessage Payload not 21 bytes
     */
    public function testTooShortPayloadThrowsException()
    {
        new BasicTrackerPayload("WTSDWXgcsbw3H5hD");
    }

    /**
     * @expectedException \Application\Exceptions\InvalidPayloadException
     * @expectedExceptionMessage Empty payload given
     */
    public function testNoPayloadThrowsException()
    {
        new BasicTrackerPayload("");
    }
}
