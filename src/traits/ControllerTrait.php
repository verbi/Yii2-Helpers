<?php
namespace verbi\yii2Helpers\traits;

use \yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */
trait ControllerTrait {

    public function getActions() {
        $actions = [];
        foreach($this->getMethods() as $methodName) {
            if(strpos($methodName,'action')===0 && $methodName !== 'actions') {
                $actionName = lcfirst(substr($methodName,6));
                $actions[$actionName] = $this->createAction($actionName);
            }
        }
        return array_merge($actions, $this->actions());
    }
    
    public function createAction($id) {
        $action = parent::createAction($id);
        if ($action == null && preg_match('/^[a-z0-9\\-_]+$/', $id) && strpos($id, '--') === false && trim($id, '-') === $id) {
            $methodName = 'action' . str_replace(' ', '', ucwords(implode(' ', explode('-', $id))));

            if ($this->hasMethod($methodName)) {
                return new \verbi\yii2Helpers\base\InlineAction($id, $this, $methodName);
            }
        }
        return $action;
    }

    public function bindActionParams($action, $params) {
        if ($action instanceof \yii\base\InlineAction && $this->hasMethod('getReflectionMethod')) {
            $method = $this->getReflectionMethod($action->actionMethod);
        }
        elseif( $action instanceof \yii\base\InlineAction ) {
            return parent::bindActionParams($action, $params);
        }
        else {
            $method = new \ReflectionMethod($action, 'run');
        }
        $args = [];
        $missing = [];
        $actionParams = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = $actionParams[$name] = (array) $params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $actionParams[$name] = $params[$name];
                } else {
                    throw new BadRequestHttpException(\Yii::t('yii', 'Invalid data received for parameter "{param}".', [
                        'param' => $name,
                    ]));
                }
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $actionParams[$name] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }
        if (!empty($missing)) {
            throw new BadRequestHttpException(\Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => implode(', ', $missing),
            ]));
        }
        $this->actionParams = $actionParams;
        return $args;
    }
    
    public function getPkFromRequest() {
        $modelClass = $this->modelClass;
        $pk = [];
        foreach ($modelClass::primaryKey(true) as $key) {
            $val = \Yii::$app->request->get($key,\Yii::$app->request->post($key));
            if($val === null) {
                return null;
            }
            $pk[$key] = $val;
        }
        return $pk;
    }
    
    public function loadModel($id = null) {
        $modelClass = $this->modelClass;
        if ($id!==null) {
            $model = $modelClass::findOne($id);
            if ($model === null) {
                throw new NotFoundHttpException;
            }
            return $model;
        }
        return new $modelClass;
    }
}
