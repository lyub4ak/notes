<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task}}`.
 */
class m200923_095257_create_task_table extends Migration
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

        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey(),
            'note_id' => $this->integer()->notNull(),
            'text' => $this->string()->notNull(),
            'priority' => $this->tinyInteger()->notNull(),
            'date' => $this->date()->notNull(),
            'is_done' => $this->boolean()->notNull()->defaultValue(0),
            'created_by_id' => $this->bigInteger()->notNull(),
            'updated_by_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'is_deleted' => $this->boolean()->notNull()->defaultValue(0),
        ]);

        $this->createIndex(
            'idx-task-note_id',
            '{{%task}}',
            'note_id'
        );
        $this->addForeignKey(
            'fk-task-note_id',
            '{{%task}}',
            'note_id',
            '{{%note}}',
            'id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-task-note_id',
            '{{%task}}'
        );
        $this->dropIndex(
            'idx-task-note_id',
            '{{%task}}'
        );

        $this->dropTable('{{%task}}');
    }
}
