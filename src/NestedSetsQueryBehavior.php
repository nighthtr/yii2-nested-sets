<?php

namespace nighthtr\nestedsets;

class NestedSetsQueryBehavior extends \creocoder\nestedsets\NestedSetsQueryBehavior
{
    public function tree($root = false, $depth = false)
    {
        $model = new $this->owner->modelClass();

        $columns = [$model->leftAttribute => SORT_ASC];

        if ($root && $model->treeAttribute !== false) {
            $columns = [$model->treeAttribute => SORT_ASC] + $columns;

            $this->owner->andWhere([$model->treeAttribute => $root->{$model->primaryKey()[0]}]);
        }

        if (!$model->treeAttribute) {
            $this->owner->andWhere(['>', $model->depthAttribute, 0]);
        }

        if ($depth) {
            $this->owner->andWhere(['<=', $model->depthAttribute, $depth]);
        }

        $this->owner
            ->indexBy($model->primaryKey()[0])
            ->addOrderBy($columns);

        return $this->owner;
    }
}