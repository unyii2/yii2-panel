<?php


namespace unyii2\yii2panel;


class Controller extends \yii\base\Controller
{
    public function render($view, $params = [])
    {
        return $this->getView()->render($view, $params, $this);
    }
}