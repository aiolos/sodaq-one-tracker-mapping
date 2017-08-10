<?php
namespace ApplicationTest\Controller;

use ApplicationTest\AbstractTestCase;

class ApiControllerTest extends AbstractTestCase
{
    protected $traceError = false;

    public function testPostActionAsGetIsInvalid()
    {
        $this->dispatch('/api/post', 'GET');
        $this->assertResponseStatusCode(400);
        $this->assertEquals(
            '{"status":"error","message":"Request should be a post"}',
            $this->getResponse()->getContent()
        );
    }

    public function testPostActionAsPostWithoutHeaderIsInvalid()
    {
        $this->getRequest()->setContent("Some content");
        $this->dispatch('/api/post', 'POST');
        $this->assertResponseStatusCode(401);
        $this->assertEquals(
            '{"status":"error","message":"Wrong authentication header"}',
            $this->getResponse()->getContent()
        );
    }

    public function testPostActionAsPostWithEmptyContentIsInvalid()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Authorization', 'yourAuthHeaderValue');
        $this->dispatch('/api/post', 'POST');
        $this->assertResponseStatusCode(400);
        $this->assertEquals(
            '{"status":"error","message":"No content in payload"}',
            $this->getResponse()->getContent()
        );
    }

    public function testPostActionAsPostWithWrongContentIsInvalid()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Authorization', 'yourAuthHeaderValue');
        $this->getRequest()->setContent("Some content");
        $this->dispatch('/api/post', 'POST');
        $this->assertResponseStatusCode(400);
        $this->assertEquals(
            '{"status":"error","message":"Request content could not be decoded"}',
            $this->getResponse()->getContent()
        );
    }

    public function testPostActionAsPostWithContentIsValid()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'phpunit';
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Authorization', 'yourAuthHeaderValue');
        $this->getRequest()->setContent(file_get_contents(__DIR__ . '/../../../../data/testdata/request.json'));
        $this->dispatch('/api/post', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertEquals(
            '{"status":"ok"}',
            $this->getResponse()->getContent()
        );
    }

    public function testDecodeActionCanBeAccessed()
    {
        $this->dispatch('/api/decode', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertEquals('[]', $this->getResponse()->getContent());
    }

    public function testPayloadActionCanBeAccessed()
    {
        $this->dispatch('/api/payload', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertEquals('[]', $this->getResponse()->getContent());
    }

    public function testInvalidRouteDoesNotCrash()
    {
        $this->dispatch('/api/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
