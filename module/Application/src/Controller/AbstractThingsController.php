<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;

abstract class AbstractThingsController extends AbstractActionController
{
    protected $entityManager;

    protected $config;

    protected $logger;

    /**
     * @param EntityManager $entityManager
     * @param $config
     * @param \Zend\Log\Logger $logger
     */
    public function __construct(EntityManager $entityManager, $config, $logger)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
}
