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

    public function testInvalidRouteDoesNotCrash()
    {
        $this->dispatch('/invalid/route', 'GET');
        $this->assertResponseStatusCode(404);
    }
}
