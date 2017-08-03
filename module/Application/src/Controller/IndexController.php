<?php

namespace Application\Controller;

use Application\Entity\Message;
use Application\Entity\Payload;
use Doctrine\ORM\Query;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
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
     * Save the complete content of the post in a Payload object
     * @return JsonModel
     */
    public function postAction()
    {
        /** @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setStatusCode(400);
            return new JsonModel(['status' => 'error', 'message' => 'You should do a post']);
        }
        if (!$request->getHeader($this->config['theThingsNetwork']['authHeaderKey'])
            || $request->getHeader($this->config['theThingsNetwork']['authHeaderKey'])->getFieldValue()
            !== $this->config['theThingsNetwork']['authHeaderValue']
        ) {
            $this->getResponse()->setStatusCode(403);
            return new JsonModel(['status' => 'error', 'message' => 'Wrong authentication header']);
        }

        $this->logger->info('Post request coming from: ' . $_SERVER['REMOTE_ADDR'] . '; User Agent: ' . $_SERVER['HTTP_USER_AGENT']);

        // Validate
        $schemaPath = __DIR__ . '/../../../../data/schema/request.json';
        $validator = new \JsonSchema\Validator;
        if (!file_exists($schemaPath)) {
            $this->logger->emerg('Scheme file could not be found: ' . $schemaPath);
            return new JsonModel(['status' => 'error', 'message' => 'Schema file cannot be found']);
        }
        try {
            $requestContent = Json::decode(($request->getContent()));
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(400);
            $this->logger->err('Request content could not be decoded: ' . $exception->getTraceAsString());
            return new JsonModel(['status' => 'error', 'message' => 'Request content could not be decoded']);
        }

        $schema = Json::decode(file_get_contents($schemaPath));
        $validator->validate(
            $requestContent,
            $schema
        );

        if (!$validator->isValid()) {
            $this->logger->info("JSON does not validate. Violations:");
            foreach ($validator->getErrors() as $error) {
                $this->logger->info(sprintf("[%s] %s", $error['property'], $error['message']));
            }
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
