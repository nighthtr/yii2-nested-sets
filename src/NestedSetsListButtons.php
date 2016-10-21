<?php

namespace nighthtr\nestedsets;

use yii\grid\ActionColumn;

class NestedSetsListButtons extends ActionColumn
{
    private static $_index = 0;

    public function renderData($model, $key) {
        self::$_index++;

        return $this->renderDataCellContent($model, $key, self::$_index);
    }
}