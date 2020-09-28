<?php

namespace common\models\enum;

use yii2mod\enum\helpers\BaseEnum;

/**
 * Priority types.
 */
class Priority extends BaseEnum
{
    const NONE = 1;
    const LOW = 2;
    const MIDDLE = 3;
    const HIGH = 4;

    public static $list = [
        self::NONE => 'None',
        self::LOW => 'Low',
        self::MIDDLE => 'Middle',
        self::HIGH => 'High',
    ];
}