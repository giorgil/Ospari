<?php

namespace OspariAdmin\Model;

Class Media extends \NZ\ActiveRecord{
    
    public function getTableName() {
        return OSPARI_DB_PREFIX.'media';
    }
    
    public function getLarge(){
        return '/content/upload'.$this->large;
    }
    public function getThumb(){
        return '/content/upload'.$this->thumb;
    }

        static public function getPager( $nzmap, $req, $perPage = 20 ){
     
        $where = array();
        
        
        return new \NZ\Pager( new Media() , $where, $page = $req->getInt('page'), $perPage, $order = 'id DESC');
        
    }
    
}