<?php


namespace unyii2\yii2panel;


use Exception;
use Yii;
use yii\base\Widget;
use yii\web\ForbiddenHttpException;

class PanelWidget extends Widget
{

    public $name;

    public $params = [];

    private $panelControllers = [];

    public function init()
    {
        parent::init();
        $this->panelControllers = Yii::$app->controller->module->panels[$this->name]
            ?? Yii::$app->params['panelWidget'][Yii::$app->controller->module->id][$this->name]
            ?? [];
    }


    /**
     * @return string
     * @throws Exception
     */
    public function run()
    {
        if(!$this->panelControllers){
            return '';
        }
        /**
         * on exception no rolled back to main controller
         */
        $oldController = Yii::$app->controller;
        $result = '';
        foreach ($this->panelControllers as $panelController) {
            $route = $panelController['route'];

            $configParams = $panelController['params'] ?? [];
            foreach ($this->params as $paramName => $paramValue) {
                $configParams[$paramName] = $paramValue;
            }
            try {
                $result .= Yii::$app->runAction($route, $configParams);
            }catch (ForbiddenHttpException $e ){
                Yii::$app->controller = $oldController;
                //its ok - no access
            }catch (Exception $exception){
                Yii::$app->controller = $oldController;
                throw $exception;
            }
        }
        return $result;
    }
}