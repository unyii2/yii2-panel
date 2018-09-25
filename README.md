yii2 panel controller
==========

[![Total Downloads](https://img.shields.io/packagist/dt/unyii2/yii2-panel.svg?style=flat-square)](https://packagist.org/packages/unyii2/yii2-panel) 

Simply dashboard solution. Each dashboard panel define in controller identicaly as Yii page:
* in behaviors can control access - panel display only for users, who has access
* create action. Can use parameters also 
* create view folder in same folder, where all other controller views


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
                'class' => AccessControl::className(),
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
