<?php

/**
 * Description of ComponentType
 *
 * @author fon-pah
 */

namespace OspariAdmin\Model;

class ComponentType extends \NZ\ActiveRecord {

    public function getTableName() {
        return OSPARI_DB_PREFIX . 'component_types';
    }

    /**
     * html is default
     * @return bool
     */
    public function isDefault() {
        return ($this->name == 'html');
    }

    public function getValidator() {
        return self::fetchValidator($this->name);
    }

    static protected function fetchValidator($name) {
        $vs = array(
            'youtube_video' => '\OspariAdmin\Validator\Youtube',
            'twitter_tweet' => '\OspariAdmin\Validator\Tweet',
            'vimeo_video' => '\OspariAdmin\Validator\Vimeo',
            'instagram' => '\OspariAdmin\Validator\Vimeo',
            'html' => '\OspariAdmin\Validator\Instagram',
            'text' => '\OspariAdmin\Validator\Text',
            'facebook_post' => '\OspariAdmin\Validator\Facebook',
            'google_plus_post	' => '\OspariAdmin\Validator\GooglePlus',
         
        );
        
        if( isset( $vs[$name] ) ){
            $className = $vs[$name];
            return new $className();
        }
        return new \OspariAdmin\Validator\Component();
    }

}
