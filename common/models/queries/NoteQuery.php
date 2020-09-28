<?php

namespace common\models\queries;

use common\models\Note;

/**
 * This is the ActiveQuery class for [[\common\models\Note]].
 *
 * @see \common\models\Note
 */
class NoteQuery extends \yii\db\ActiveQuery
{
    /**
     * @return self
     */
    public function notDeleted()
    {
        return $this->andWhere([Note::tableName().'.is_deleted' => false]);
    }
}
