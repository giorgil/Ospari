<?php

namespace OspariAdmin\Model;

Class Draft extends \NZ\ActiveRecord {

    const STATE_PUBLISHED = 1;
    const STATE_UNPUBLISHED =0;

    public function getTableName() {
        return OSPARI_DB_PREFIX.'drafts';
    }
    
    public function getUrl() {
        return OSPARI_URL . '/post/' . $this->slug;
    }
    
    public function getEditUrl() {
        return '/'.OSPARI_ADMIN_PATH . '/draft/components/edit/' . $this->id;
    }

    static public function getPager($map, $req, $perPage = 20) {
        $where = new \Zend\Db\Sql\Where();
        if( $map->user_id ){
            $where->equalTo('user_id', $map->user_id);
        }
        
        if($map->like){
            $where->like('title', '%'.$map->like.'%');
        }
        return new \NZ\Pager(new Draft(), $where, $req->getInt('page'), $perPage, $order = 'id DESC');
    }

    public function isPublished() {
        return ($this->state == self::STATE_PUBLISHED);
    }
    
    
    
}
