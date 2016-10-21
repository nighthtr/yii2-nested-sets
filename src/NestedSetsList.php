<?php

namespace nighthtr\nestedsets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class NestedSetsList extends \yii\base\Widget
{
    public $model;
    public $options = [];
    public $pluginEvents = [];
    public $pluginOptions = [];
    public $actionColumn = [
        'class' => 'nighthtr\nestedsets\NestedSetsListButtons',
    ];

    private $_items;
    private $_defauktPluginOptions = [
        'maxDepth' => 5,
        'group' => 0,
        'listNodeName' => 'ol',
        'itemNodeName' => 'li',
        'rootClass' => 'dd',
        'listClass' => 'dd-list',
        'itemClass' => 'dd-item',
        'dragClass' => 'dd-dragel',
        'handleClass' => 'dd-handle',
        'collapsedClass' => 'dd-collapsed',
        'placeClass' => 'dd-placeholder',
        'emptyClass' => 'dd-empty',
        'expandBtnHTML' => '<button data-action="expand" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>',
        'collapseBtnHTML' => '<button data-action="collapse" type="button"><span class="glyphicon glyphicon-minus" aria-hidden="true"></span></button>',
        'threshold' => 20,
    ];

    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if (is_array($this->actionColumn) && isset($this->actionColumn['class'])) {
            $this->actionColumn = Yii::createObject($this->actionColumn);
        }

        $this->pluginEvents = ArrayHelper::merge(
            [
                'change' => 'function(event, element) {
                    var element = $(element);
                    var parent = element.parents("li");
                    var prev = element.prev("li");
                    var next = element.next("li");
                    $.ajax({
                        url: "nodeMove",
                        context: document.body,
                        data: {
                            id  : element.data(\'id\'),
                            parent_id : (parent.length ? parent.data(\'id\') : 0),
                            lft : (prev.length ? prev.data(\'id\') : 0),
                            rgt : (next.length ? next.data(\'id\') : 0)
                        }
                    }).fail(function(jqXHR){
                        if (jqXHR.status == 500) {
                            alert(jqXHR.responseJSON.message);
                        } else {
                            alert(jqXHR.responseText);
                        }
                    });
                }',
            ],
            $this->pluginEvents
        );

        $this->pluginOptions = ArrayHelper::merge($this->_defauktPluginOptions, $this->pluginOptions);
    }

    public function run()
    {
        $id = $this->options['id'];
        $options = Json::htmlEncode($this->pluginOptions);
        $this->_items = $this->model->treeItems();
        $view = $this->getView();
        NestedSetsListAssets::register($view);
        $view->registerJs("jQuery('#$id').nestable($options);");
        foreach ($this->pluginEvents as $event => $function) {
            $view->registerJs("$('#$id').on('$event', $function);");
        }
        return Html::tag('div', $this->renderTree(), ['id' => $this->options['id'], 'class' => $this->pluginOptions['rootClass']]);
    }

    public function renderTree()
    {
        $html = '';
        $level = 0;

        if ($this->model->treeAttribute) {
            $html .= Html::beginTag($this->pluginOptions['listNodeName'], ['class' => $this->pluginOptions['listClass']]);
        }

        foreach ($this->_items as $key => $item) {
             if ($level > $item[$this->model->depthAttribute]) {
                for ($i=$level-$item[$this->model->depthAttribute]; $i > 0; $i--) {
                    $html .= Html::endTag($this->pluginOptions['itemNodeName']);
                    $html .= Html::endTag($this->pluginOptions['listNodeName']);
                }
            } elseif ($level < $item[$this->model->depthAttribute]) {
                $html .= Html::beginTag($this->pluginOptions['listNodeName'], ['class' => $this->pluginOptions['listClass']]);
            } else {
                $html .= Html::endTag($this->pluginOptions['itemNodeName']);
            }

            $html .= Html::beginTag($this->pluginOptions['itemNodeName'], ['data' => ['id' => $item[$this->model->primaryKey()[0]]], 'class' => $this->pluginOptions['itemClass']]);
            $html .= Html::tag('div', '<span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span>', ['class' => $this->pluginOptions['handleClass']]);
            $html .= Html::tag('div', $item[$this->model->titleAttribute]
                . Html::tag('div', $this->actionColumn->renderData($item, $key), ['class' => 'pull-right']), ['class' => 'dd-content']);

            $level = $item[$this->model->depthAttribute];
        }

        for ($i=$this->model->treeAttribute ? $level+1 : $level; $i > 0; $i--) {
            $html .= Html::endTag($this->pluginOptions['itemNodeName']);
            $html .= Html::endTag($this->pluginOptions['listNodeName']);
        }

        return $html;
    }
}
