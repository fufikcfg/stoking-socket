<?php

namespace app\modules\websocket;

use app\models\User;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class Server implements MessageComponentInterface
{
    private SplObjectStorage $clients;

    public function __construct(
        private readonly AuthService $authService,
        private readonly ConnectionLogger $logger
    ) {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        try {
            $message = $this->parseMessage($msg);

            if ($this->isAlreadyAuthenticated($from)) {
                return;
            }

            $this->handleAuthentication($from, $message);
        } catch (\LogicException $e) {
            $this->handleInvalidMessage($from, $e->getMessage());
        } catch (\LogicException $e) {
            $this->handleFailedAuth($from, $e->getMessage());
        }
    }

    private function parseMessage(string $msg): array
    {
        $data = json_decode($msg, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \LogicException('Invalid JSON format');
        }

        return $data;
    }

    private function isAlreadyAuthenticated(ConnectionInterface $connection): bool
    {
        return isset($connection->userId);
    }

    private function handleAuthentication(ConnectionInterface $from, array $message): void
    {
        if (empty($message['token'])) {
            throw new \LogicException('Token is required');
        }

        $user = $this->authService->authenticate($message['token']);

        if (!$user) {
            throw new \LogicException('Invalid token');
        }

        $this->setupConnection($from, $user, $message);
        $this->sendSuccessResponse($from);
    }

    private function setupConnection(ConnectionInterface $from, User $user, array $message): void
    {
        $from->userId = $user->id;
        $from->token = $message['token'];
        $from->userAgent = $message['user_agent'] ?? 'Unknown';
        $from->logId = $this->logger->connect(
            $user->id,
            $from->token,
            $from->userAgent
        );
    }

    private function sendSuccessResponse(ConnectionInterface $from): void
    {
        $from->send(json_encode([
            'auth' => 'success',
            'user_id' => $from->userId,
            'session_id' => $from->logId
        ]));
    }

    private function handleInvalidMessage(ConnectionInterface $from, string $error): void
    {
        $from->send(json_encode([
            'error' => $error,
            'code' => 400
        ]));
        $from->close();
    }

    private function handleFailedAuth(ConnectionInterface $from, string $error): void
    {
        $from->send(json_encode([
            'auth' => 'fail',
            'error' => $error,
            'code' => 401
        ]));
        $from->close();
    }

    public function onClose(ConnectionInterface $conn): void
    {
        if (isset($conn->userId, $conn->token)) {
            $this->logger->disconnect($conn->userId);
        }
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $conn->close();
    }
}