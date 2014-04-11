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
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request');
        }
        if(!$req->isPOST()){
            return $res->sendErrorMessageJSON('Bad Request method');
        }
        $cmpType = Model\ComponentType::findOne(array('id'=>$req->type_id));
        if(!$cmpType){
            return $res->sendErrorMessageJSON('Invalid Component type!');
        }
        try {
            $component = new Model\Component();
            if(!is_callable(array($component,$cmpType->validator),true)){
               return $res->sendErrorMessageJSON('Validator no found!');
            }
            $component = $component->{$cmpType->validator}($req , $component);
            $component->user_id = $this->getUser()->id;
            $component->save();
            $obj = new \stdClass();
            $obj->success=true;
            $obj->data = $component->toArray();
            return $res->sendJson( json_encode($obj) );
        } catch (\Exception $exc) {
            return $res->sendErrorMessageJSON($exc->getMessage());
        }
            
    }
    
    public function editAction( HttpRequest $req, HttpResponse $res ){
        
        
    }
    
    
    

    
}