<?php

namespace nighthtr\nestedsets;

use Yii;
use yii\base\Action;
use yii\db\ActiveQuery;

class NodeMoveAction extends Action
{
    public $modelName;

    public function run($id = 0, $lft = 0, $rgt = 0, $parent_id = 0)
    {
        Yii::$app->response->format = 'json';

        if ($this->modelName === null) {
            throw new \yii\base\InvalidConfigException("No 'modelName' supplied on action initialization.");
        }

        $model  = $this->modelName::findOne($id);
        $lft    = $this->modelName::findOne($lft);
        $rgt    = $this->modelName::findOne($rgt);
        $parent = $this->modelName::findOne($parent_id);

        if ($model->treeAttribute && $parent === null && !$model->isRoot()) {
            $model->moveNodeAsRoot();
        } elseif (!$parent) {
            if ($rgt) {
                $model->insertBefore($rgt);
            } elseif ($lft) {
                $model->insertAfter($lft);
            }
        } else {
            if ($rgt) {
                $model->insertBefore($rgt);
            } elseif ($lft) {
                $model->insertAfter($lft);
            } else {
                $model->appendTo($parent);
            }
        }

        return ['updated' => [
            'id' => $model->id,
            'depth' => $model->{$model->depthAttribute},
            'lft' => $model->{$model->leftAttribute},
            'rgt' => $model->{$model->rightAttribute},
        ]];
    }

}