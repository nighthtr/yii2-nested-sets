<?php

namespace nighthtr\nestedsets;

use Yii;
use yii\base\Action;
use yii\web\Response;

class ChildrenAction extends Action
{
    public $modelName;

    public function run($id = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $models = $this->modelName::findOne($id)->children(1)->all();

        $children = [];
        foreach ($models as $key => $model) {
            $children[] = [
                'key' => $model->id,
                'title' => $model->name,
                'lazy' => !$model->isLeaf(),
            ];
        }

        return $children;
    }
}