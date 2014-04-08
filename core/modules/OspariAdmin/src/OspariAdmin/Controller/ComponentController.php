<?php


/**
 * Description of Component
 *
 * @author 28h.eu
 */

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;
use OspariAdmin\Model;

class ComponentController extends BaseController {
    
    public function addAction( HttpRequest $req, HttpResponse $res ){
        
        $obj = $this->validate($req);
        $component = new Model\Component();
        $component->comment = $obj->comment;
        $component->code = $obj->code;
        $component->type_id = $obj->type_id;
        $component->user_id = $component->user_id;
        $component->setCreatedAt();
        $component->save();
        
        $obj->success = TRUE;
        return $res->sendJson( json_encode($obj) );
        
    }
    
    public function editAction( HttpRequest $req, HttpResponse $res ){
        
        
    }
    
    /**
     * 
     * @param type $req
     * @return \stdClass
     */
    protected function validate( $req ){
     
        //to do 
        $type_id = 1;
        
        $std = new \stdClass();
        $std->comment = $req->comment;
        $std->code = $req->code;
        $std->type_id = $type_id;
        return $std;
    }
    
}