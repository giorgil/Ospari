<?php

/**
 * Description of ComponentType
 *
 * @author fon-pah
 */
namespace OspariAdmin\Model;
class ComponentType extends \NZ\ActiveRecord{
     public function getTableName() {
       return OSPARI_DB_PREFIX.'component_types';
    }
    
    /**
     * html is default
     * @return bool
     */
    public function isDefault(){
        return ($this->name == 'html');
    }
}
