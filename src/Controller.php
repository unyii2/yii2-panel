<?php


namespace unyii2\yii2panel;


class Controller extends \yii\web\Controller
{
    public $params = [];

    public function render($view, $params = [])
    {
        $filePath = $this->getViewPath() . DIRECTORY_SEPARATOR . $view;
        if (pathinfo($filePath, PATHINFO_EXTENSION) === '') {
            $filePath .=  '.php';
        }
        return $this->renderFile($filePath, $params);
    }

//    public function getViewPath()
//    {
//        return $this->module->getViewPath();// . DIRECTORY_SEPARATOR . $this->id;
//
//    }
}
