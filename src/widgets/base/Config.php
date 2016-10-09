<?php
namespace verbi\yii2Helpers\widgets\base;

/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class Config extends \kartik\base\Config {
    protected static $_validInputWidgets = [
        '\kartik\typeahead\Typeahead' => ['yii2-widgets', 'yii2-widget-typeahead'],
        '\kartik\select2\Select2' => ['yii2-widgets', 'yii2-widget-select2'],
        '\kartik\depdrop\DepDrop' => ['yii2-widgets', 'yii2-widget-depdrop'],
        '\kartik\touchspin\TouchSpin' => ['yii2-widgets', 'yii2-widget-touchspin'],
        '\kartik\switchinput\SwitchInput' => ['yii2-widgets', 'yii2-widget-switchinput'],
        '\kartik\rating\StarRating' => ['yii2-widgets', 'yii2-widget-rating'],
        '\kartik\file\FileInput' => ['yii2-widgets', 'yii2-widget-fileinput'],
        '\kartik\range\RangeInput' => ['yii2-widgets', 'yii2-widget-rangeinput'],
        '\kartik\color\ColorInput' => ['yii2-widgets', 'yii2-widget-colorinput'],
        '\kartik\date\DatePicker' => ['yii2-widgets', 'yii2-widget-datepicker'],
        '\kartik\time\TimePicker' => ['yii2-widgets', 'yii2-widget-timepicker'],
        '\kartik\datetime\DateTimePicker' => ['yii2-widgets', 'yii2-widget-datetimepicker'],
        '\kartik\daterange\DateRangePicker' => 'yii2-date-range',
        '\kartik\sortinput\SortableInput' => 'yii2-sortinput',
        '\kartik\money\MaskMoney' => 'yii2-money',
        '\kartik\checkbox\CheckboxX' => 'yii2-checkbox',
        '\kartik\slider\Slider' => 'yii2-slider',
        '\borales\extensions\phoneInput\PhoneInput' =>  ['yii2-widgets', 'yii2-phone-input'],
    ];
    
    /**
     * Validate a single extension dependency
     *
     * @param string $name the extension class name (without vendor namespace prefix)
     * @param mixed  $repo the extension package repository names (without vendor name prefix)
     * @param string $reason a user friendly message for dependency validation failure
     *
     * @throws InvalidConfigException if extension fails dependency validation
     */
    public static function checkDependency($name = '', $repo = '', $reason = self::DEFAULT_REASON)
    {
        if (empty($name)) {
            return;
        }
        $command = "php composer.phar require " . self::VENDOR_NAME;
        $version = ": \"@dev\"";
        $class = $name;

        if (is_array($repo)) {
            $repos = "one of '" . implode("' OR '", $repo) . "' extensions. ";
            $installs = $command . implode("{$version}\n\n--- OR ---\n\n{$command}", $repo) . $version;
        } else {
            $repos = "the '" . $repo . "' extension. ";
            $installs = $command . $repo . $version;
        }

        if (!class_exists($class)) {
            throw new InvalidConfigException(
                "The class '{$class}' was not found and is required {$reason}.\n\n" .
                "Please ensure you have installed {$repos}" .
                "To install, you can run this console command from your application root:\n\n{$installs}"
            );
        }
    }
}