<?php

namespace Application\Controller;

use Application\Entity\Message;
use Application\Entity\Payload;
use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;

class IndexController extends AbstractActionController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager, $config)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
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
                    $gateways[$gateway->gtw_id] = [
                        'latitude' => $gateway->latitude,
                        'longitude' => $gateway->longitude
                    ];
                }
            }
        }
        $gatewaysJsArray = 'var gatewayMarkers = [';
        foreach ($gateways as $gatewayId => $gateway) {
            if (strlen($gateway['latitude']) > 0 && strlen($gateway['longitude']) > 0) {
                $gatewaysJsArray .= '["';
                $gatewaysJsArray .= $gatewayId . '", ';
                $gatewaysJsArray .= $gateway['latitude'] . ', ';
                $gatewaysJsArray .= $gateway['longitude'] . '],';
            }
        }

        $markers .= '];';
        $gatewaysJsArray .= '];';

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
     * Save the complete content of the post in a Payload object
     * @return JsonModel
     */
    public function postAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return new JsonModel(['status' => 'error', 'message' => 'You should do a post']);
        }
        if ($request->getHeader($this->config['theThingsNetwork']['authHeaderKey'])->getFieldValue()
            !== $this->config['theThingsNetwork']['authHeaderValue']
        ) {
            return new JsonModel(['status' => 'error', 'message' => 'Wrong authentication header']);
        }
        $payload = new Payload();
        $payload->setDateCreated(new \DateTime());

        $payload->setContent($request->getContent());
        $payload->setStatus(Payload::STATUS_NEW);
        $this->entityManager->persist($payload);
        $this->entityManager->flush();

        $this->createMessage($payload);

        return new JsonModel(['status' => 'ok']);
    }

    /**
     * Decode the content of a Payload object
     * @return JsonModel
     */
    public function decodeAction()
    {
        $updated = [];
        $payloads = $this->entityManager->getRepository(Payload::class)->findBy(['status' => Payload::STATUS_NEW]);
        foreach ($payloads as $payload) {
            $message = $this->createMessage($payload);

            $updated[] = ['id' => $message->getId()];
        }

        return new JsonModel($updated);
    }

    /**
     * Decode the raw payload of the message coming from the Sodaq.
     * The decoded payload is added to the already existing message
     * @return JsonModel
     */
    public function payloadAction()
    {
        $updated = [];
        /** @var Message $message */
        $messages = $this->entityManager->getRepository(Message::class)->findBy(['decodedPayload' => ['', null]]);

        foreach ($messages as $message) {
            $decoded = Message::decodeRawPayload($message->getRawPayload());
            $message->setDecodedPayload(json_encode($decoded));
            $this->entityManager->persist($message);

            $updated[] = ['id' => $message->getId()];
        }
        $this->entityManager->flush();

        return new JsonModel($updated);
    }

    /**
     * Create a message from the given payload
     * @param Payload $payload
     * @return Message
     */
    private function createMessage($payload)
    {
        $message = Message::createFromPayload($payload);
        $payload->setStatus(Payload::STATUS_PROCESSED);
        $this->entityManager->persist($message);
        $this->entityManager->persist($payload);
        $this->entityManager->flush();

        return $message;
    }
}
