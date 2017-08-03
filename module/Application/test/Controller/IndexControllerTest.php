<?php
namespace ApplicationTest\Controller;

use Application\Controller\IndexController;
use ApplicationTest\AbstractTestCase;

class IndexControllerTest extends AbstractTestCase
{
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(IndexController::class);
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }

    public function testIndexActionViewModelTemplateRenderedWithinLayout()
    {
        $this->dispatch('/', 'GET');
        $this->assertQuery('#messages');
    }

    public function testRequestActionCanBeAccessed()
    {
        $this->dispatch('/application/request', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(IndexController::class);
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('application');
    }

    public function testRequestActionViewModelTemplateRenderedWithinLayout()
    {
        $this->dispatch('/application/request', 'GET');
        $this->assertQuery('#requests');
    }

    public function testPostActionAsGetIsInvalid()
    {
        $this->dispatch('/application/post', 'GET');
        $this->assertResponseStatusCode(400);
        $this->assertEquals(
            '{"status":"error","message":"You should do a post, and it should have content"}',
            $this->getResponse()->getContent()
        );
    }

    public function testPostActionAsPostWithoutHeaderIsInvalid()
    {
        $this->getRequest()->setContent("Some content");
        $this->dispatch('/application/post', 'POST');
        $this->assertResponseStatusCode(403);
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
        $this->dispatch('/application/post', 'POST');
        $this->assertResponseStatusCode(400);
        $this->assertEquals(
            '{"status":"error","message":"You should do a post, and it should have content"}',
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
        $this->dispatch('/application/post', 'POST');
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
        $this->dispatch('/application/post', 'POST');
        $this->assertResponseStatusCode(200);
        $this->assertEquals(
            '{"status":"ok"}',
            $this->getResponse()->getContent()
        );
    }

    public function testDecodeActionCanBeAccessed()
    {
        $this->dispatch('/application/decode', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertEquals('[]', $this->getResponse()->getContent());
    }

    public function testPayloadActionCanBeAccessed()
    {
        $this->dispatch('/application/payload', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertEquals('[]', $this->getResponse()->getContent());
    }

    public function testInvalidRouteDoesNotCrash()
    {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
