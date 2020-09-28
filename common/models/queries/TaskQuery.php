<?php

namespace common\models\queries;

use common\models\Task;

/**
 * This is the ActiveQuery class for [[\common\models\Task]].
 *
 * @see \common\models\Task
 */
class TaskQuery extends \yii\db\ActiveQuery
{
    /**
     * @return self
     */
    public function notDeleted()
    {
        return $this->andWhere([Task::tableName().'.is_deleted' => false]);
    }
}
