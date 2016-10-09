<?php
namespace verbi\yii2Profile\behaviors;


use yii\base\Behavior;
use \Yii;
/*
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-Helpers/
 * @license https://opensource.org/licenses/GPL-3.0
*/
class SectionBehavior extends Behavior {
    public $_sections = [];
    
    public function hasSection($id)
    {
        if (($pos = strpos($id, '/')) !== false) {
            // sub-module
            $section = $this->owner->getSection(substr($id, 0, $pos));
            return $section === null ? false : $section->hasSection(substr($id, $pos + 1));
        } else {
            return isset($this->owner->_sections[$id]);
        }
    }
    
    public function getSection($id, $load = true) {
        if (($pos = strpos($id, '/')) !== false) {
            // sub-module
            $section = $this->owner->getSection(substr($id, 0, $pos));
            return $section === null ? null : $section->getSection(substr($id, $pos + 1), $load);
        }
        if (isset($this->owner->_sections[$id])) {
            if ($this->owner->_sections[$id] instanceof Section) {
                return $this->owner->_sections[$id];
            } elseif ($load) {
                Yii::trace("Loading section: $id", __METHOD__);
                /* @var $section Section */
                $section = Yii::createObject($this->owner->_sections[$id], [$id, $this->owner]);
                $section->setInstance($section);
                $this->owner->setSection($id, $section);
                return $this->owner->_sections[$id];
            }
        }
        return null;
    }
    
    public function getSections($loadedOnly = false)
    {
        if ($loadedOnly) {
            $sections = [];
            foreach ($this->owner->_sections as $section) {
                if ($section instanceof Section) {
                    $sections[] = $section;
                }
            }
            return $sections;
        } else {
            return $this->owner->_sections;
        }
    }
    
    public function setSections( $sections )
    {
        foreach ( $sections as $id => $section ) {
            $this->owner->setSection( $id, $section );
        }
    }
    
    public function setSection( $id, $value )
    {
        $this->_sections[$id] = $value;
        $this->owner->setModule( $id, $value );
    }
}