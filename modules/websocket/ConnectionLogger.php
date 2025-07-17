<?php

namespace app\modules\websocket;

use app\models\WsConnection;
use yii\db\Expression;

class ConnectionLogger
{
    public function connect(int $userId, string $token, string $userAgent): int
    {
        $wsConnection = new WsConnection([
            'user_id' => $userId,
            'used_token' => $token,
            'user_agent' => $userAgent,
            'connected_at' => time(),
        ]);
        $wsConnection->save(false);
        return $wsConnection->id;
    }

    public function disconnect(int $userId): void
    {
        $lastConnection = WsConnection::find()
            ->where([
                'user_id' => $userId,
                'disconnected_at' => null
            ])
            ->orderBy(['connected_at' => SORT_DESC])
            ->one();
        $lastConnection->disconnected_at = time();
        $lastConnection->save(false);
    }
}