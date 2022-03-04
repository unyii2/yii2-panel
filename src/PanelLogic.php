<?php

namespace unyii2\yii2panel;

use Exception;
use Yii;
use yii\base\Component;
use yii\web\ForbiddenHttpException;

/**
 * search defined panels controllers and execute
 */
class PanelLogic extends Component
{

    /** @var array controller action parameters */
    public $params = [];

    /** @var string panel name */
    public $name;

    public $panelControllers = [];

    public function run(): array
    {
        if (!$this->panelControllers) {
            $this->panelControllers = [];
        }
        $panelControllers = $this->loadPanelControllers();
        if (!$panelControllers) {
            return [];
        }

        /**
         * on exception no rolled back to main controller
         */
        $oldController = Yii::$app->controller;
        foreach ($panelControllers as $key => $panelController) {
            $route = $panelController['route'];

            $configParams = $panelController['params'] ?? [];
            foreach ($this->params as $paramName => $paramValue) {
                $configParams[$paramName] = $paramValue;
            }
            try {
                $panelControllers[$key]['result'] = Yii::$app->runAction($route, $configParams);
            } catch (ForbiddenHttpException $e) {
                Yii::$app->controller = $oldController;
                //its ok - no access
            } catch (Exception $exception) {
                Yii::$app->controller = $oldController;
                throw $exception;
            }
        }
        Yii::$app->controller = $oldController;
        return $panelControllers;

    }

    /**
     * @return array
     */
    private function loadPanelControllers(): array
    {
        $panelControllers = [];
        $leadingController = Yii::$app->controller;
        if (isset($leadingController->panels) && ($leadingController->panels[$this->name] ?? false)) {
            foreach ($leadingController->panels[$this->name] as $controller) {
                $panelControllers[] = $controller;
            }
        }
        $module = $leadingController->module;
        if (isset($module->panels) && ($module->panels[$this->name] ?? false)) {
            foreach ($module->panels[$this->name] as $controller) {
                $panelControllers[] = $controller;
            }
        }
        if (Yii::$app->params['panelWidget'][$module->id][$this->name] ?? false) {
            foreach (Yii::$app->params['panelWidget'][$module->id][$this->name] as $controller) {
                $panelControllers[] = $controller;
            }
        }
        return array_merge($this->panelControllers, $panelControllers);
    }
}