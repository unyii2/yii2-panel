<?php

namespace unyii2\yii2panel;

use Yii;

class Controller extends \yii\web\Controller
{
    public array $params = [];

    public function render($view, $params = []): string
    {
        $filePath = $this->getViewPath() . DIRECTORY_SEPARATOR . $view;
        if (pathinfo($filePath, PATHINFO_EXTENSION) === '') {
            $filePath .=  '.php';
        }
        if (strpos($filePath,'@') === 0) {
            $filePath = Yii::getAlias($filePath);
        }
        return $this->renderFile($filePath, $params);
    }
}
