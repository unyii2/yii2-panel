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

    public $panelControllers = [];

    public function init()
    {
        parent::init();

        $leadingController = Yii::$app->controller;
        if(isset($leadingController->panels) && ($leadingController->panels[$this->name]??false)){
            foreach ($leadingController->panels[$this->name] as $controller){
                $this->panelControllers[] = $controller;
            }
        }
        $module = $leadingController->module;
        if(isset($module->panels) &&  ($module->panels[$this->name]??false)){
            foreach ($module->panels[$this->name] as $controller){
                $this->panelControllers[] = $controller;
            }
        }
        if(Yii::$app->params['panelWidget'][$module->id][$this->name]??false){
            foreach (Yii::$app->params['panelWidget'][$module->id][$this->name] as $controller){
                $this->panelControllers[] = $controller;
            }
        }
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