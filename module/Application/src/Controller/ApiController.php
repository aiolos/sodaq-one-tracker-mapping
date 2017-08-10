<?php

namespace Application\Controller;

use Application\Entity\Message;
use Application\Entity\Payload;
use Application\Exceptions\AuthenticationException;
use Application\Exceptions\InvalidPayloadException;
use Application\Exceptions\ThingsException;
use Doctrine\ORM\Query;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Doctrine\ORM\EntityManager;

class ApiController extends AbstractActionController
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager, $config, $logger)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        /** @var \Zend\Log\Logger logger */
        $this->logger = $logger;
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
            return $this->createErrorResponse('Request should be a post', 400);
        }
        if (empty($this->getRequest()->getContent())) {
            return $this->createErrorResponse('No content in payload', 400);
        }
        if (!$request->getHeader($this->config['theThingsNetwork']['authHeaderKey'])
            || $request->getHeader($this->config['theThingsNetwork']['authHeaderKey'])->getFieldValue()
            !== $this->config['theThingsNetwork']['authHeaderValue']
        ) {
            return $this->createErrorResponse('Wrong authentication header', 401);
        }

        $this->logger->info(
            'Post request coming from: ' . $_SERVER['REMOTE_ADDR'] . '; User Agent: ' . $_SERVER['HTTP_USER_AGENT']
        );

        try {
            $requestContent = Json::decode(($request->getContent()));
        } catch (\Exception $exception) {
            $this->logger->err('Request content could not be decoded: ' . $exception->getTraceAsString());

            return $this->createErrorResponse('Request content could not be decoded', 400);
        }
        $this->validatePayload($requestContent);

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

    private function validatePayload($requestContent)
    {
        $schemaPath = __DIR__ . '/../../../../data/schema/request.json';
        $validator = new \JsonSchema\Validator;
        if (!file_exists($schemaPath)) {
            $this->logger->emerg('Scheme file could not be found: ' . $schemaPath);
            throw new ThingsException('Schema file cannot be found', 500);
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
    }

    private function createErrorResponse($message, $statusCode)
    {
        $this->getResponse()->setStatusCode($statusCode);
        return new JsonModel(['status' => 'error', 'message' => $message]);
    }
}
