<?php
namespace verbi\yii2Helpers\behaviors\fileUpload;
use \verbi\yii2Helpers\behaviors\base\Behavior;

/* 
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class FileUploadModelBehavior extends Behavior {
    public $thumbnailUrl = [];
    public $deleteUrl = [];
    public $url;
    public $name;
    
    public function getThumbnailUrl() {
        return $this->owner->thumbnailUrl;
    }
    
    public function getDeleteUrl() {
        return $this->owner->deleteUrl;
    }
    
    public function getSize() {
        return 0;
    }
}