<?php

namespace Application\Controller;

use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

abstract class AbstractThingsController extends AbstractActionController
{
    protected $entityManager;

    protected $config;

    protected $logger;

    public function __construct(EntityManager $entityManager, $config, $logger)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        /** @var \Zend\Log\Logger logger */
        $this->logger = $logger;
    }
}
