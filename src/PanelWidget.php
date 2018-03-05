<?php


namespace unyii2\yii2panel;


use yii\base\Widget;

class PanelWidget extends Widget
{

    public $name;

    public $action;
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
        foreach ($this->panelControllers as $panelController){
            if(!is_array($panelController)){
                $panelController['class'] = $panelController;
            }

            $controller = \Yii::createObject($panelController['class']);
            unset($panelController['class']);
            foreach($this->params as $paramName => $paramValue){
                $panelController[$paramName] = $paramValue;
            }
            $result .= $controller->runAction($this->action, $panelController);
        }

        return $result;
    }
}