<?php

namespace nighthtr\nestedsets;

class NestedSetsBehavior extends \creocoder\nestedsets\NestedSetsBehavior
{
    public $titleAttribute = 'title';

    public function treeItems($root = false, $depth = false)
    {
        return $this->owner->find()->tree($root, $depth)->asArray()->all();
    }
}