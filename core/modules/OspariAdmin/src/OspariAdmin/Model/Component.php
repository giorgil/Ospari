<?php

namespace OspariAdmin\Model;
use NZ\HttpRequest;
use NZ\HttpResponse;
Class Component extends \NZ\ActiveRecord {
     public function getTableName() {
       return OSPARI_DB_PREFIX.'components';
    }
    
    /**
     * 
     * @param type $req
     * @return \stdClass
     */
    public function validate(HttpRequest $req, $model){
        if(!$comment =$req->comment ){
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