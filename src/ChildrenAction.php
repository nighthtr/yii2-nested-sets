<?php

namespace nighthtr\nestedsets;

use Yii;
use yii\base\Action;
use yii\web\Response;

class ChildrenAction extends Action
{
    public $modelName;
    public $titleAttribute = 'title';
    public $filterAttribute;
    public $filterAttributeValue;

    public function run($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($this->modelName === null) {
            throw new \yii\base\InvalidConfigException("No 'modelName' supplied on action initialization.");
        }

        $children = [];

        $query = $this->modelName::find();

        $query->where($id ? ['id' => $id] : ['depth' => 0]);

        if ($this->filterAttribute) {
            $query->andWhere([$this->filterAttribute => $this->filterAttributeValue ? $this->filterAttributeValue : null]);
        }

        if (($parent = $query->one()) !== null) {
            $models = $parent->children(1)->all();

            foreach ($models as $key => $model) {
                $children[] = [
                    'key' => $model->id,
                    'title' => $model->{$this->titleAttribute},
                    'lazy' => !$model->isLeaf(),
                ];
            }
        }

        return $children;
    }
}