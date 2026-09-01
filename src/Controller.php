<?php

namespace unyii2\yii2panel;

use Yii;

class Controller extends \yii\web\Controller
{
    public array $params = [];

    public function render($view, $params = []): string
    {
        if (strpos($view,'@') === 0) {
            $filePath = Yii::getAlias($view);
        } else {
            $filePath = $this->getViewPath() . DIRECTORY_SEPARATOR . $view;
        }
        if (pathinfo($filePath, PATHINFO_EXTENSION) === '') {
            $filePath .=  '.php';
        }
        return $this->view->renderPhpFile($filePath, $params);
    }
}
