<?php


namespace unyii2\yii2panel;


use yii\base\Widget;

class PanelWidget extends Widget
{

    public $name;

    public $params = [];

    private $panelController;

    public function init()
    {
        parent::init();
        $this->panelControllers = Yii::$app->controller->module->panels[$this->name];
    }


    public function run()
    {
        $result = '';
        foreach ($this->panelControllers as $panelController) {
            $controller = \Yii::createObject($panelController['class']);
            $action = $panelController['action'];
            $configParams = $panelController['params'] ?? [];
            foreach ($this->params as $paramName => $paramValue) {
                $configParams[$paramName] = $paramValue;
            }
            $result .= $controller->runAction($action, $configParams);
        }

        return $result;
    }
}