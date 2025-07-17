<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%ws_connections}}`.
 */
class m250717_095114_create_ws_connections_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%ws_connections}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'used_token' => $this->string()->notNull(),
            'user_agent' => $this->string(),
            'connected_at' => $this->dateTime()->notNull(),
            'disconnected_at' => $this->dateTime(),
        ]);
        $this->createIndex(
            'idx-ws_connections-user_id',
            '{{%ws_connections}}',
            'user_id'
        );
        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey(
                'fk-ws_connections-user_id',
                '{{%ws_connections}}',
                'user_id',
                '{{%users}}',
                'id',
                'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->db->driverName !== 'sqlite') {
            $this->dropForeignKey(
                'fk-ws_connections-user_id',
                '{{%ws_connections}}'
            );
        }
        $this->dropIndex(
            'idx-ws_connections-user_id',
            '{{%ws_connections}}'
        );
        $this->dropTable('{{%ws_connections}}');
    }
}
