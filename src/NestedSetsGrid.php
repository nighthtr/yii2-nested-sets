<?php

namespace nighthtr\nestedsets;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\View;
use yii\web\JsExpression;

class NestedSetsGrid extends \yii\base\Widget
{
    public $modelName;
    public $titleAttribute = 'title';
    public $filterAttribute;
    public $actions = [];

    public $options = [
        'class' => 'table table-condensed table-hover table-striped fancytree-fade-expander',
    ];

    public $pluginOptions = [];
    public $pluginEvents = [];

    private $_defaultPluginOptions = [
        'extensions' => ['dnd', 'edit', 'glyph', 'table'],
        'table' => [
            'nodeColumnIdx' => 1,
        ],
    ];

    public $glyph_opts = [
        'map' => [
            'doc' => 'glyphicon glyphicon-file',
            'docOpen' => 'glyphicon glyphicon-file',
            'checkbox' => 'glyphicon glyphicon-unchecked',
            'checkboxSelected' => 'glyphicon glyphicon-check',
            'checkboxUnknown' => 'glyphicon glyphicon-share',
            'dragHelper' => 'glyphicon glyphicon-play',
            'dropMarker' => 'glyphicon glyphicon-arrow-right',
            'error' => 'glyphicon glyphicon-warning-sign',
            'expanderClosed' => 'glyphicon glyphicon-menu-right',
            'expanderLazy' => 'glyphicon glyphicon-menu-right',
            'expanderOpen' => 'glyphicon glyphicon-menu-down',
            'folder' => 'glyphicon glyphicon-folder-close',
            'folderOpen' => 'glyphicon glyphicon-folder-open',
            'loading' => 'glyphicon glyphicon-refresh glyphicon-spi',
        ],
    ];

    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if (!isset($this->actions['children'])) {
            $this->actions['children'] = Url::to(['children', $this->filterAttribute => Yii::$app->request->get($this->filterAttribute)]);
        }

        if (!isset($this->actions['nodeMove'])) {
            $this->actions['nodeMove'] = Url::to(['nodeMove']);
        }

        if (!isset($this->actions['view'])) {
            $this->actions['view'] = Url::to(['view']);
        }

        if (!isset($this->actions['update'])) {
            $this->actions['update'] = Url::to(['update']);
        }

        if (!isset($this->actions['delete'])) {
            $this->actions['delete'] = Url::to(['delete']);
        }

        $this->_defaultPluginOptions = ArrayHelper::merge([
            'glyph' => new JsExpression("glyph_opts"),
            'dnd' => [
                'dragStart' => new JsExpression("function(node, data) {
                    return true;
                }"),
                'dragEnter' => new JsExpression("function(node, data) {
                    return true;
                }"),
                'dragDrop' => new JsExpression("function(node, data) {
                    $.ajax({
                        url: '" . $this->actions['nodeMove'] . "',
                        dataType: 'json',
                        data: {
                            id: data.otherNode.key,
                            target: node.key,
                            action: data.hitMode
                        },
                        success: function(response){
                            if (response.status) {
                                data.otherNode.moveTo(node, data.hitMode);
                            } else {
                                alert(response);
                            }
                        },
                        error: function(response){
                            alert(response);
                        }
                    });
                }"),
            ],
            'source' => new JsExpression("{url: '" . $this->actions['children'] . "'}"),
            'activate' => new JsExpression("function(event, data) { data.tree.activateKey(false) }"),
            'lazyLoad' => new JsExpression("function(event, data) {
                data.result = {url: '" . $this->actions['children'] . "', data: {id: data.node.key}};
            }"),
            'renderColumns' => new JsExpression("function(event, data) {
                var node = data.node;
                \$tdList = $(node.tr).find('>td');
                \$tdList.eq(0).text(node.key);
                \$tdList.eq(2).html('<a href=\"" . $this->actions['view'] . "?id=' + node.key + '\" title=\"Просмотр\" aria-label=\"Просмотр\" data-pjax=\"0\"><span class=\"glyphicon glyphicon-eye-open\"></span></a>\
                    <a href=\"" . $this->actions['update'] . "?id=' + node.key + '\" title=\"Редактировать\" aria-label=\"Редактировать\" data-pjax=\"0\"><span class=\"glyphicon glyphicon-pencil\"></span></a>\
                    <a href=\"" . $this->actions['delete'] . "?id=' + node.key + '\" title=\"Удалить\" aria-label=\"Удалить\" data-pjax=\"0\" data-confirm=\"Вы уверены, что хотите удалить этот элемент?\" data-method=\"post\">\
                        <span class=\"glyphicon glyphicon-trash\"></span>\
                    </a>');
            }"),
        ], $this->_defaultPluginOptions);

        $this->pluginOptions = ArrayHelper::merge($this->pluginOptions, $this->_defaultPluginOptions);
    }

    public function run()
    {
        $model = new $this->modelName;
        $id = $this->options['id'];
        $options = Json::htmlEncode($this->pluginOptions);
        $view = $this->getView();

        NestedSetsGridAssets::register($view);

        $view->registerJs('glyph_opts = ' . Json::htmlEncode($this->glyph_opts) . ';', View::POS_END);
        $view->registerJs("jQuery('#$id').fancytree($options);");

        foreach ($this->pluginEvents as $event => $function) {
            $view->registerJs("$('#$id').on('$event', $function);");
        }

        return Html::tag('table', '<colgroup>
                <col width="80px"></col>
                <col width="*"></col>
                <col width="67px"></col>
            </colgroup>
            <thead>
                <tr>
                    <th>' . $model->getAttributeLabel('id') . '</th>
                    <th>' . $model->getAttributeLabel($this->titleAttribute) . '</th>
                    <th class="action-column"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>', $this->options);
    }
}
