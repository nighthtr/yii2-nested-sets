yii2 nested sets extension
=====================
yii2 nested sets extension

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nighthtr/yii2-nested-sets "*"
```

or add

```
"nighthtr/yii2-nested-sets": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

```php
<?= \nighthtr\nestedsets\widget\NestedSetsWidget::widget(); ?>
```