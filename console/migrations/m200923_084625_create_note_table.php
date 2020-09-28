<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%note}}`.
 */
class m200923_084625_create_note_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%note}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'text' => $this->text()->null(),
            'priority' => $this->tinyInteger()->notNull(),
            'is_done' => $this->boolean()->notNull()->defaultValue(0),
            'created_by_id' => $this->bigInteger()->notNull(),
            'updated_by_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            'idx-note-user_id',
            '{{%note}}',
            'user_id'
        );
        $this->addForeignKey(
            'fk-note-user_id',
            '{{%note}}',
            'user_id',
            '{{%user}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-note-user_id',
            '{{%note}}'
        );
        $this->dropIndex(
            'idx-note-user_id',
            '{{%note}}'
        );

        $this->dropTable('{{%note}}');
    }
}
