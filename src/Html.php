<?php

namespace verbi\yii2Helpers;

use Yii;
use yii\db\ActiveRecordInterface;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
 */

class Html extends \kartik\helpers\Html {

    public static function pageHeading($title, $options = []) {
        return static::tag('h1', static::encode($title), $options);
    }

    public static function widgetHeading($title, $options = []) {
        return static::tag('h2', static::encode($title), $options);
    }

    public static function beginPageWrapperDivision($options = []) {
        return static::beginTag('div', $options);
    }

    public static function endPageWrapperDivision() {
        return static::endTag('div');
    }

    public static function showFlash($options = []) {
        $output = '';
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            $modOptions = $options;
            $modOptions['class'] = 'alert alert-' . $key . (isset($modOptions['class']) ? ' ' . $modOptions['class'] : '');
            $output .= static::div($message, $modOptions);
        }
        return $output;
    }

    public static function paragraph($text, $options = []) {
        return static::tag('p', static::encode($text), $options);
    }

    public static function div($text, $options = []) {
        return static::tag('div', $text, $options);
    }

    public static function fieldset($text, $options = []) {
        return static::tag('fieldset', (isset($options['legend']) ? static::tag('legend', $options['legend']) : '') . $text);
    }

    public static function tag($tag, $content = '', $options = []) {
        if ($tag !== false)
            return parent::tag($tag, $content, $options);
        return $content;
    }

    public static function img($src, $options = []) {
        $lazyload = true;
        if (isset($options['lazyLoad'])) {
            $lazyload = $options['lazyLoad'];
            unset($options['lazyLoad']);
        }

        if ($lazyload) {
            $options['src'] = \yii\helpers\Url::to($src);
            if (!isset($options['alt'])) {
                $options['alt'] = '';
            }
            return \verbi\yii2Helpers\widgets\LazyLoad::widget($options);
        }
        return parent::img($src, $options);
    }

    public static function hidden($text, $options = []) {
        return static::tag('div', $text, array_merge(['hidden' => 'hidden',], $options));
    }

    public static function translate($message, $options = [], string $source = 'app') {
        if (is_array($message)) {
            $v = array_shift($message);
            $options = array_merge($options, $message);
            $message = $v;
        }
        return Yii::t($source, $message, $options);
    }

    public static function getAttributeValue($model, $attribute, $json_encode = true) {
        if ($json_encode) {
            $value = parent::getAttributeValue($model, $attribute);
        } else {
            if (!preg_match(static::$attributeRegex, $attribute, $matches)) {
                throw new InvalidArgumentException('Attribute name must contain word characters only.');
            }
            $attribute = $matches[2];
            $value = $model->$attribute;
            if ($matches[3] !== '') {
                foreach (explode('][', trim($matches[3], '[]')) as $id) {
                    if ((is_array($value) || $value instanceof \ArrayAccess) && isset($value[$id])) {
                        $value = $value[$id];
                    } else {
                        return null;
                    }
                }
            }
            
            if (is_array($value)) {
                foreach ($value as $i => $v) {
                    if ($v instanceof ActiveRecordInterface) {
//                        $v = $v->getPrimaryKey(false);
                        $value[$i] = (string) $v;
                    }
                }
            } elseif ($value instanceof ActiveRecordInterface) {
//                $value = $value->getPrimaryKey(false);
                return (string) $value;
            }
        }
        return $value;
    }

}
