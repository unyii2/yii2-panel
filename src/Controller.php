<?php


namespace unyii2\yii2panel;


class Controller extends \yii\base\Controller
{
    public $params = [];

    public function render($view, $params = [])
    {
        return $this->getView()->render($view, $params, $this);
    }

//    public function getViewPath()
//    {
//        return $this->module->getViewPath();// . DIRECTORY_SEPARATOR . $this->id;
//
//    }
}