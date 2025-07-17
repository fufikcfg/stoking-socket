<?php

namespace app\commands;

use app\modules\websocket\AuthService;
use app\modules\websocket\ConnectionLogger;
use app\modules\websocket\Server;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use yii\console\Controller;

class WebSocketController extends Controller
{
    public function actionStart(): void
    {
        IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Server(new AuthService(), new ConnectionLogger())
                )
            ),
            8080
        )->run();
    }
}