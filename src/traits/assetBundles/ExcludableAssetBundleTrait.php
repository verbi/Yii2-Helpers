<?php
namespace verbi\yii2Helpers\traits\assetBundles;
trait ExcludableAssetBundleTrait {
    protected $excludeJs = false;
    protected $excludeCss = false;
    
    public function getExcludeJs() {
        return $this->excludeJs;
    }
    
    public function getExcludeCss() {
        return $this->excludeCss;
    }
    
    public function getAllDepends($bundle = null) {
        if($bundle == null) {
            $bundle = $this;
        }
        $allDepends = [];
        foreach($bundle->depends as $depend) {
            $depends = \Yii::$app->getAssetManager()->getBundle($depend);
            $allDepends[$depends->className()] = $depends;
            
            $allDepends = array_merge($allDepends, $this->getAllDepends($depends));
        }
        return $allDepends;
    }
}