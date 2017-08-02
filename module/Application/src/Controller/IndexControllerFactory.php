<?php
namespace Application\Controller;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $entityManager = $container->get(EntityManager::class);

        /** @var \Zend\Log\Logger $logger */
        $logger = $container->get('ThingsLogger');

        return new IndexController($entityManager, $config, $logger);
    }
}
