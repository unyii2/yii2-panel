<?php

namespace unyii2\yii2panel;

use Exception;
use yii\base\Widget;
use yii\helpers\Html;

class PanelWidget extends Widget
{

    /** @var string panel name */
    public $name;

    /** @var array controller action parameters */
    public $params = [];

    public $panelControllers;

    /**
     * @return string
     * @throws Exception
     */
    public function run()
    {

        $logic = new PanelLogic([
            'params' => $this->params,
            'name' => $this->name,
            'panelControllers' => $this->panelControllers
        ]);

        if (!$panelControllers = $logic->run()) {
            return '';
        }

        /**
         * on exception no rolled back to main controller
         */
        $result = '';
        foreach ($panelControllers as $panelController) {
            $panelResult = $panelController['result'] ?? '';
            if (!$panelResult) {
                continue;
            }
            $panel = $panelResult;
            if (isset($panelController['tag'])) {
                $panel = Html::tag($panelController['tag'], $panel, $panelController['options'] ?? []);
            }
            $result .= $panel;
        }
        return $result;
    }

}