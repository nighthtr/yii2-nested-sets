<?php

namespace nighthtr\nestedsets;

use Yii;
use yii\base\Action;
use yii\web\Response;

class NodeMoveAction extends Action
{
    public $modelName;
    public $treeAttribute;

    public function run($id, $target, $action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($this->modelName === null) {
            throw new \yii\base\InvalidConfigException("No 'modelName' supplied on action initialization.");
        }

        $model  = $this->modelName::findOne($id);
        $target = $this->modelName::findOne($target);

        if ($this->treeAttribute && ($model->$this->treeAttribute != $target->$this->treeAttribute)) {
            return ['status' => false];
        }

        switch ($action) {
            case 'over':
                return ['status' => $model->appendTo($target)];
                break;

            case 'before':
                return ['status' => $model->insertBefore($target)];
                break;

            case 'after':
                return ['status' => $model->insertAfter($target)];
                break;
        }

        return ['status' => false];
    }

}