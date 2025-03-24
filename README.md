yii2 panel controller
==========

[![Total Downloads](https://img.shields.io/packagist/dt/unyii2/yii2-panel.svg?style=flat-square)](https://packagist.org/packages/unyii2/yii2-panel) 

Yii2Panel was designed to make boards that display different panels from different modules/extensions with access rights control.

Meeting better as expected, as the Yii2Panel can be used for displaying panel from any other module/extension with access control.

Another benefit is that the panels to be displayed are assigned to a module configuration that allows different panels to be used in the module for different projects.

For procesing submited data from PanelWidgwet van use PanleLogic

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

# Controller action 
For processing submited data form panel widget can use PanelLogic
```php
    public function actionCreate()
    {
    
        $model = new RkInvoiceForm();
        if($model->load($request->post()) && $model->save()) {
            $panelLogic = Yii::createObject([
                'class' => PanelLogic::class,
                'name' => 'LietvedibaRkInvoiceSave',
                'params' => [
                    'modelRecordId' => $model->id
                ]
            ]);
            $panelLogic->run();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
       
    }
```

# Module config

To module add parameter 'panels' and in configuration for module add panels routes
```php
        'invoices' => [
            'class' => 'd3modules\d3invoices\Module',
            'panels' => [
                /** for widget */
                'exportSettings' => [
                    [
                        'route' => 'd3accexport/invoice-panel/document-settings',
                        'params' => [
                            'docId' => 13 // action parameter value
                         ]
                        'tag' => 'div', // optinal. Add enclosing tag to panel  
                        'options' => ['class' => 'col-sm-8 col-md-6 col-lg-4'] //enclosing tag options
                     ]
                 ],
                 /** for panel logic */
                 'LietvedibaRkInvoiceSave' => [
                    [
                        'route' => 'deal/panel/save'
                    ]
                ],
            ],
        ],

```
To controller add parameter 'panels' and in configuration for module add panels routes
```php
        'invoices' => [
            'class' => 'd3modules\d3invoices\Module',
            'controllerMap' => [
                'settings' => [
                    'class' => 'yii3\persons\controllers\SettingsController',
                    'panels' => [
                        'UserSettingsProvileLeft' =>
                            [
                                [
                                    'route' => 'myauth/panel/show-qrc'
                                 ]
                            ]
                    ]
                ],
            ],
```
Optionally, if no possible add to module parameter 'panels', panel routes can define in parameters

```php
'params' => [
    'panelWidget' => [
        'dashboard' => [
            'last' =>  [
                [
                    'route' => 'delivery/panel/transit-declarations',
                    /**  
                     * parameters for action method:
                     * public function actionMyTransitDeclarations(array $statusIdList): string 
                     */
                    'params' => [
                        'statusIdList' => [5, 20]
                    ]                    
                ]
            ],
        ]
    ],
]

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
                    /**
                    * standard definition
                    */
                    [
                        'allow' => true,
                        'actions' => [
                            'document-settings',
                        ],
                        'roles' => [
                            'DocSetting',
                        ],
                    ],
                    
                    /**
                    * roles define in panel module config. 
                    *   Example of edi module config:
                    *        'edi' => [
                    *            'class' => 'd3yii2\d3edi\Module',
                    *            'accessRulesMessageRoles' => ['Depo3EdiFull']
                    *        ],
                    *   In Module add property:
                    *        class Module extends D3Module
                    *           public $accessRulesMessageRoles;
                    *           ....
                    */
                    [
                        'allow' => true,
                        'actions' => [
                            'message',
                        ],
                        'roles' => $this->module->rulesMessageRoles??['@'],
                    ],                    
                ],
            ],
        ];
    }

    /** for widget */
    public function actionDocumentSettings()
    {
        return $this->render('setting_grid',[]);

    }

    /** for widget */
    public function actionMessage()
    {
        return $this->render('message',[]);

    }

    /** for controller logic */
    public function actionSave(int $modelRecordId): bool
    {
        if (!$model = DealInvoice::findOne(['invoice_id' => $modelRecordId])) {
            $model = new DealInvoice();
            $model->invoice_id = $modelRecordId;
        }

        $request = Yii::$app->request;
        if ($model->load($request->post())
            && $model->deal_id
            && $model->save()
        ) {
            return true;
        }

        if ($model->hasErrors()) {
            throw new \Exception(json_encode($model->getErrors()));
        }
        return true;
    }

}
```
# panel usage as data transfering between modules

## to module panels add panel action
```php
$config = [
    'modules' => [
        'invoices' => [
            'class' => 'd3modules\d3invoices\Module',
            'panels' => [
                'invoice-items-woff' => [
                    [
                        'route' => 'd4storei/data-panel/woff',
                    ]
            ]         
        ]
    ]

```

## executing panel
Can use for getting data or executing something in other module
```php

            $panelLogic = new PanelLogic([
                'name' => 'invoice-items-woff',
                'params' => [
                    'invoiceSysModelId' => $sysModelId,
                    'invoiceId' => $model->id,
                    'items' => $items,
                ],
            ]);
            $panelLogicData = $logic->run();


```

## panel controller
```php
use unyii2\yii2panel\Controller;

/**
 * @property Module $module
 */
class DataPanelController extends Controller
{

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    /**
                     * standard definition
                     */
                    [
                        'allow' => true,
                        'actions' => [
                            'woff',
                        ],
                        'roles' => [
                            D3ModulesD4StoreiFullUserRole::NAME,
                        ],
                    ],
                ],
            ],
        ];
    }

            /**
             * masīvs pielāgots InvInvoiceItems::load()
             */
            $list[] = [
                'name' => $row->storeProduct->product->name,
                'count' => $row->productQnt,
        }
        return $list;
    }

    /**
     *  write off invoice items
     *  Add refs:
     *  - inv_invoice
     *  - inv_invoice_items
     *  - user
     *
     * @param int $invoiceSysModelId
     * @param int $invoiceId
     * @param array<int, array{
     *     itemSysModelId: int,
     *     itemId: int,
     *     count:float,
     *     unitId: int,
     *     storeProductSysModelId: int,
     *     storeProductId: int
     * }> $items
     * @return bool
     * @throws D3ActiveRecordException
     * @throws Exception
     */
    public function actionWoff(
        int $invoiceSysModelId,
        int $invoiceId,
        array $items
    ): bool
    {
        $storeProductSysModelId = SysModelsDictionary::getIdByClassName(D4StoreStoreProduct::class);
        if (!$transaction = Yii::$app->db->beginTransaction()) {
            throw new Exception('Can not initiate transaction');
        }
        try {
            foreach ($items as $item) {
                if ((int)$item['storeProductSysModelId'] !== $storeProductSysModelId) {
                    throw new Exception('Wrong store product model sys id: ' . $storeProductSysModelId);
                }
                $product = D4StoreStoreProduct::findOne($item['storeProductId']);
                $action = new Action($product);
                $action->outSys($item['count'],  $item['itemSysModelId'], $item['itemId']);
                $action->addRef(Yii::$app->user);
                $action->addRefSys($invoiceSysModelId, $invoiceId);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            FlashHelper::addDanger($e->getMessage());
            Yii::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return false;
        }
        return true;
    }
}


```

