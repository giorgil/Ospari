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
        
        return new \NZ\Pager(new Component(), $where, $req->getInt('page'), $perPage, $order = 'id DESC');
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

    



    /**
     * 
     * @param type $req
     * @return \stdClass
     */
    public function validate(HttpRequest $req, $model){
        if(!$comment = $req->comment ){
            throw new \Exception('Comment required');
        }
        $model->comment = $comment;
        if(!$type_id =$req->type_id){
            throw new \Exception('Component type required');
        }
        $model->type_id = $type_id;
        if(!$draft_id =$req->getRouter('draft_id')){
            throw new \Exception('Draft ID required');
        }
        $model->draft_id =$draft_id;
        $model->order_nr = $this->getOrderNr($draft_id);
        
        $model->setCreatedAt();
        return $model;
    }
    
    public function validateEmbedCode(HttpRequest $req, $model){
        $model = $this->validate($req, $model);
        if(!$code =$req->code ){
            throw new \Exception('Code required');
        }
        if(!$code= $this->removeScript($code)){
             throw new \Exception('Code could not be parsed!');
        }
        $model->code = $code;
        return $model;
    }


    protected function removeScript($string){
        $dom = new \DOMDocument();
        $dom->loadHTML($string);
        while (($r = $dom->getElementsByTagName("script")) && $r->length) {
            $r->item(0)->parentNode->removeChild($r->item(0));
        }
        $body = $dom->getElementsByTagName('body')->item(0);
        $code = $dom->saveHTML($body);
        //without the script twitter pictures are no loaded
        return str_replace(array('<body>','</body>'), '',$code);
    }
    
    protected function getOrderNr($draft_id){
        $model =  new Component();
        $nr = $model->count(array('draft_id'=>$draft_id));
        $nr = $nr+1;
        return $nr;
    }
}