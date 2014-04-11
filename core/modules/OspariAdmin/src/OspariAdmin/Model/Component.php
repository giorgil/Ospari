<?php

namespace OspariAdmin\Model;
use NZ\HttpRequest;
use NZ\HttpResponse;
Class Component extends \NZ\ActiveRecord {
    
    protected static $types = array();
    protected static $typesLoaded = FALSE;
    protected static $defaultType = NULL;


    public function getTableName() {
       return OSPARI_DB_PREFIX.'components';
    }
    
    
    static public function getPager($map, $req, $perPage = 100) {
        $where = new \Zend\Db\Sql\Where();
        /*
        if( $map->draft_id ){
            $where->equalTo('draft_id', $map->draft_id);
        }
         * 
         */
        $where->equalTo('draft_id', $req->getInt('draft_id'));
        
        return new \NZ\Pager(new Component(), $where, $req->getInt('page'), $perPage, $order = 'order_nr ASC');
    }
    
    public function getType(){
        return self::fetchType($this->type_id);
    }

    

    static protected function fetchType( $type_id ){
        $types = self::$types; 
       if( !self::$typesLoaded ){
          
           foreach( ComponentType::findAll(array()) as $type ){
               $types[$type->id] = $type;
               if( $type->isDefault() ){
                   self::$defaultType = $type;
               }
           }
          self::$types = $types;
           self::$typesLoaded = TRUE;
       } 
       
       if( isset( $types[$type_id ] ) ){
           return $types[$type_id ];
       }
       return  self::$defaultType;
       
    }
    
    public function getOrderNr($draft_id){
        $model =  new Component();
        $nr = $model->count(array('draft_id'=>$draft_id));
        $nr = $nr+1;
        return $nr;
    }
}