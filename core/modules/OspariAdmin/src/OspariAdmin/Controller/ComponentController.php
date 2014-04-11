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
        if(!$draft_id = $req->getRouter('draft_id')){
            return $res->sendErrorMessageJSON('Invalid Draft ID!');
        }
        
        $cmpType = Model\ComponentType::findOne(array('id'=>$req->type_id));
        if(!$cmpType){
            return $res->sendErrorMessageJSON('Invalid Component type!');
        }
        
        try {
            $component = new Model\Component();

            $map = new \NZ\Map(array('component'=> $component,'draft_id'=> $draft_id,'componentType'=>$cmpType));
            $component = $this->createOrEdit($map, $req, $res);
            $obj = new \stdClass();
            $obj->success=true;
            $obj->data = $component->toArray();
            $res->setViewVar('component', $component);
            $obj->html= $res->getViewContent('tpl/draft_cmp.php');
            return $res->sendJson( json_encode($obj) );
        } catch (\Exception $exc) {
            return $res->sendErrorMessageJSON($exc->getMessage());
        }
            
    }
    
    public function editAction( HttpRequest $req, HttpResponse $res ){
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request');
        }
        if(!$req->isPOST()){
            return $res->sendErrorMessageJSON('Bad Request method');
        }
        if(!$draft_id = $req->getRouter('draft_id')){
            return $res->sendErrorMessageJSON('Invalid Draft ID!');
        }
        
        $cmpType = Model\ComponentType::findOne(array('id'=>$req->getInt('type_id')));
        if(!$cmpType){
            return $res->sendErrorMessageJSON('Invalid Component type!');
        }
        $component = new Model\Component(array('id'=>$req->getInt('component_id'),'draft_id'=>$draft_id));
        if(!$component->id){
            return $res->sendErrorMessageJSON('Component not found!');
        }
        
        try {
            $map = new \NZ\Map(array('component'=> $component,'draft_id'=> $draft_id,'componentType'=>$cmpType));
            $component = $this->createOrEdit($map, $req, $res);
            $obj = new \stdClass();
            $obj->success=true;
            $obj->data = $component->toArray();
            $res->setViewVar('component', $component);
            $obj->html= $res->getViewContent('tpl/draft_component_wrapper.php');
            return $res->sendJson( json_encode($obj) );
        } catch (\Exception $exc) {
            return $res->sendErrorMessageJSON($exc->getMessage());
        }
        
    }
    
    private function createOrEdit(\NZ\Map $map, $req, $res){
            $cmpType = $map->get('componentType');
            $draft_id = $map->get('draft_id');
            $component = $map->get('component');
            if(!class_exists($cmpType->validator,true)){
               return $res->sendErrorMessageJSON('Validator no found!');
            }
            $validator = new $cmpType->validator();
            $data = $validator->validate(new \NZ\Map(), $req);
            $component->comment = isset($data->comment)? $data->comment:'';
            $component->code = isset($data->code)? $data->code:'';
            if(!$component->id){
                $component->setCreatedAt();
                $component->user_id = $this->getUser()->id;
                $component->draft_id = $draft_id;
                $component->type_id= $cmpType->id;
                $component->order_nr = $component->getOrderNr($draft_id);
            }
            $component->save();
            return $component;
    }
    
    
    

    
}