yii2 panel controller
==========

[![Total Downloads](https://img.shields.io/packagist/dt/unyii2/yii2-panel.svg?style=flat-square)](https://packagist.org/packages/unyii2/yii2-panel) 

Yii2Panel was designed to make boards that display different panels from different modules/extensions with access rights control.

Meeting better as expected, as the Yii2Panel can be used for displaying panel from any other module/extension with access control.

Another benefit is that the panels to be displayed are assigned to a module configuration that allows different panels to be used in the module for different projects.

Realisation
-----------
Simply dashboard solution. Each dashboard panel define as panel controller action  identically as Yii page:

* panel controller in behaviors can control access - panel display only for users, who has access;
* panel controller controller action for creating HTML or response;
* create view folder in same folder, where all module controller views;
* for displaying add PanelWidget like anchor in view file;
* in module config for PanelWidget set one or more panels as Yii routes with parameters to panel controller;


Sequence
--------
* PanelWidget get panel list from module configuration
* PanelWidget call panel controller action with parameters
* Panel controller validate access. If no access, return empty string
* panel controller action create response HTML
* PanelWidget output response HTML  


Installation by composer
------------
```composer
{
    "require": {
       "unyii2/yii2-panel": "dev-master"
    }
}

Or

$ composer require unyii2/yii2-panel "dev-master"
```

# Widget 

```php

echo \unyii2\yii2panel\PanelWidget::widget([
    'name' => 'exportSettings',
    'params' => [
        'docId' => 777
    ]
]);

```

# Module config

```php
        'invoices' => [
            'class' => 'd3modules\d3invoices\Module',
            'panels' => [
                'exportSettings' =>
                [
                    [
                        'route' => 'd3accexport/invoice-panel/document-settings',
                        'params' => [
                            'docId' => 13
                         ]
                     ]
                 ]
            ],
        ],

```

# Panel controller with access control and view rendering

Standard view path: d3modules/d3accexport/views/invoice-panel/setting_grid.php

```php
<?php

namespace d3modules\d3accexport\controllers;

use unyii2\yii2panel\Controller;
use yii\filters\AccessControl;

class InvoicePanelController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'document-settings',
                        ],
                        'roles' => [
                            'DocSetting',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function actionDocumentSettings()
    {
        return $this->render('setting_grid',[]);

    }

}
```
