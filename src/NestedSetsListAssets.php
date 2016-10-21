<?php

namespace nighthtr\nestedsets;

use yii\web\AssetBundle;


class NestedSetsListAssets extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';
    public $css = [
        'jquery.nestable.css',
    ];
    public $js = [
        'jquery.nestable.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}
