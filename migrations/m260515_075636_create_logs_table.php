<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%logs}}`.
 */
class m260515_075636_create_logs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%logs}}', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(45)->notNull(),
            'requested_at' => $this->dateTime()->notNull(),
            'url' => $this->string(2048)->notNull(),
            'user_agent' => $this->text()->notNull(),
            'os' => $this->string(50),
            'architecture' => $this->string(10),
            'browser' => $this->string(50),
        ]);

        $this->createIndex('idx_logs_requested_at', '{{%logs}}', 'requested_at');
        $this->createIndex('idx_logs_os', '{{%logs}}', 'os');
        $this->createIndex('idx_logs_architecture', '{{%logs}}', 'architecture');
        $this->createIndex('idx_logs_browser', '{{%logs}}', 'browser');
    }
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%logs}}');
    }
}
