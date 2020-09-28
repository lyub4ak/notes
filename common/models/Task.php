<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $note_id
 * @property string $text
 * @property int $priority
 * @property string $date
 * @property int $is_done
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_deleted
 *
 * @property Note $note
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    public function behaviors()
    {
        return [
            'blameableBehavior' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by_id',
                'updatedByAttribute' => 'updated_by_id',
            ],
            'timestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'is_deleted' => true,
                ],
                'replaceRegularDelete' => true,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['note_id', 'text', 'priority', 'date'], 'required'],
            [['note_id', 'priority', 'is_done'], 'integer'],
            [['date'], 'safe'],
            [['text'], 'string', 'max' => 255],
            [['note_id'], 'exist', 'skipOnError' => true, 'targetClass' => Note::class, 'targetAttribute' => ['note_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'note_id' => 'Note ID',
            'text' => 'Text',
            'priority' => 'Priority',
            'date' => 'Execution Date',
            'is_done' => 'Is Done',
            'created_by_id' => 'Created By ID',
            'updated_by_id' => 'Updated By ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
        ];
    }

    /**
     * Gets query for [[Note]].
     *
     * @return \yii\db\ActiveQuery|\common\models\queries\NoteQuery
     */
    public function getNote()
    {
        return $this->hasOne(Note::class, ['id' => 'note_id'])
            ->andWhere([Note::tableName().'.is_deleted' => false]);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\queries\TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\queries\TaskQuery(get_called_class());
    }
}
