<?php

namespace unyii2\yii2panel;

use Exception;
use yii\base\Widget;
use yii\helpers\Html;

class PanelWidget extends Widget
{

    /** @var string|null panel name */
    public ?string $name = null;

    /** @var array controller action parameters */
    public array $params = [];

    public array $panelControllers = [];

    /** @var bool for getting data panel can use for collecting data */
    public bool $returnArray = false;

    /**
     * @return string|array
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
        $resultArray = [];
        foreach ($panelControllers as $panelController) {
            $panelResult = $panelController['result'] ?? '';
            if (!$panelResult) {
                continue;
            }
            if ($this->returnArray) {
                $resultArray[] = $panelResult;
                continue;
            }
            $panel = $panelResult;
            if (isset($panelController['tag'])) {
                $panel = Html::tag($panelController['tag'], $panel, $panelController['options'] ?? []);
            }
            $result .= $panel;
        }
        if ($resultArray) {
            return $resultArray;
        }
        return $result;
    }
}
