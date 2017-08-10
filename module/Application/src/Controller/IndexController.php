<?php

namespace Application\Controller;

use Application\Entity\Message;
use Application\Entity\Payload;
use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;

class IndexController extends AbstractActionController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager, $config, $logger)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        /** @var \Zend\Log\Logger logger */
        $this->logger = $logger;
    }

    public function indexAction()
    {
        $messages = $this->entityManager->getRepository(Message::class)->findBy([], ['id' => 'DESC']);

        $messagesForView = [];
        $markers = 'var sodaqMarkers = [';
        $gateways = [];
        /** @var Message $message */
        foreach ($messages as $message) {
            $messageForView = new \stdClass();
            $messageForView->deviceId = $message->getDeviceId();
            $messageForView->decodedPayload = json_decode($message->getDecodedPayload());
            $messageForView->metadata = json_decode($message->getMetadata());

            $messagesForView[] = $messageForView;
            if (!empty($messageForView->decodedPayload)
                && property_exists($messageForView->decodedPayload, 'lat')
            ) {
                $markers .= '["';
                $markers .= $message->getId() . '", ';
                $markers .= $messageForView->decodedPayload->lat . ', ';
                $markers .= $messageForView->decodedPayload->lon;
                $markers .= '],';
            }
            if (property_exists($messageForView->metadata, 'gateways')) {
                foreach ($messageForView->metadata->gateways as $gateway) {
                    if (property_exists($gateway, 'longitude')
                        && property_exists($gateway, 'latitude')
                    ) {
                        $gateways[$gateway->gtw_id] = [
                            'latitude' => $gateway->latitude,
                            'longitude' => $gateway->longitude
                        ];
                    }
                }
            }
        }
        $gatewaysJsArray = $this->createGatewaysArray($gateways);

        $markers .= '];';

        return new ViewModel([
            'messages' => $messagesForView,
            'markers' => $markers,
            'gateways' => $gatewaysJsArray,
            'apiKey' => $this->config['googleMaps']['apiKey']
        ]);
    }

    public function requestAction()
    {
        $payloads = $this->entityManager->getRepository(Payload::class)->findBy([], ['id' => 'DESC']);

        return new ViewModel(['payloads' => $payloads]);
    }

    /**
     * @param $gateways
     * @return string
     */
    private function createGatewaysArray($gateways)
    {
        $gatewaysJsArray = 'var gatewayMarkers = [';
        foreach ($gateways as $gatewayId => $gateway) {
            if (strlen($gateway['latitude']) > 0 && strlen($gateway['longitude']) > 0) {
                $gatewaysJsArray .= '["';
                $gatewaysJsArray .= $gatewayId . '", ';
                $gatewaysJsArray .= $gateway['latitude'] . ', ';
                $gatewaysJsArray .= $gateway['longitude'] . '],';
            }
        }
        $gatewaysJsArray .= '];';
        return $gatewaysJsArray;
    }
}
