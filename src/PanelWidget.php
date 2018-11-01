<?php


namespace unyii2\yii2panel;


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
        $this->panelControllers = \Yii::$app->controller->module->panels[$this->name]??[];
    }


    /**
     * @return string
     * @throws \Exception
     */
    public function run()
    {
        $result = '';
        foreach ($this->panelControllers as $panelController) {
            $route = $panelController['route'];

            $configParams = $panelController['params'] ?? [];
            foreach ($this->params as $paramName => $paramValue) {
                $configParams[$paramName] = $paramValue;
            }
            try {
                $result .= \Yii::$app->runAction($route, $configParams);
            }catch (ForbiddenHttpException $e ){
                //its ok - no access
            }catch (\Exception $exception){
                throw $exception;
            }
        }

        return $result;
    }
}