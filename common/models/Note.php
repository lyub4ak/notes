<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "note".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $text
 * @property int $priority
 * @property int $is_done
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $is_deleted
 *
 * @property User $user
 * @property Task[] $tasks
 */
class Note extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'note';
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
            [['user_id', 'name', 'priority'], 'required'],
            [['is_done'], 'default', 'value' => 0],
            [['user_id', 'priority', 'is_done'], 'integer'],
            [['text'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'text' => 'Text',
            'priority' => 'Priority',
            'is_done' => 'Is Done',
            'created_by_id' => 'Created By ID',
            'updated_by_id' => 'Updated By ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'username' => 'User Name',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\common\models\queries\UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Name of user.
     *
     * @return string
     */
    public function getUsername () {
        return $this->user->username;
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery|\common\models\queries\TaskQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['note_id' => 'id'])
            ->andWhere([Task::tableName().'.is_deleted' => false]);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\queries\NoteQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\queries\NoteQuery(get_called_class());
    }
}
