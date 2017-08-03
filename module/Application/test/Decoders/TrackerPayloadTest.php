<?php

namespace ApplicationTest\Decoders;

use Application\Controller\IndexController;
use Application\Decoders\TrackerPayload;
use ApplicationTest\AbstractTestCase;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class TrackerPayloadTest extends AbstractTestCase
{
    public function testPayloadCanBeDecoded()
    {
        $decoder = new TrackerPayload("WTSDWXgcsbw3H5hD6gIVAAEAyAD/");
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
}
