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

    public function addAction(HttpRequest $req, HttpResponse $res) {
        if (!$req->isAjax()) {
            return $res->sendErrorMessage('Bad Request');
        }
        if (!$req->isPOST()) {
            return $res->sendErrorMessageJSON('Bad Request method');
        }
        if (!$draft_id = $req->getRouter('draft_id')) {
            return $res->sendErrorMessageJSON('Invalid Draft ID!');
        }

        $cmpType = Model\ComponentType::findOne(array('id' => $req->type_id));
        if (!$cmpType) {
            return $res->sendErrorMessageJSON('Invalid Component type!');
        }

        try {
            $component = new Model\Component();

            $map = new \NZ\Map(array('component' => $component, 'draft_id' => $draft_id, 'componentType' => $cmpType));
            $component = $this->createOrEdit($map, $req, $res);

            $obj = new \stdClass();
            $obj->success = true;
            $obj->data = $component->toStdObject();
            $res->setViewVar('component', $component);
            $res->setViewVar('use_iFrame', TRUE);
            $obj->html = $this->renderCode($component, $res);

            return $res->sendJson(json_encode($obj));
        } catch (\Exception $exc) {
            return $res->sendErrorMessageJSON($exc->getMessage());
        }
    }

    protected function renderCode($component, $res, $mode = 'add') {

        if ($mode == 'edit') {
            return $res->getViewContent('tpl/draft_component_content.php');
        }

        return $res->getViewContent('tpl/draft_component.php');
    }

    public function embedAction(HttpRequest $req, HttpResponse $res) {
        //exit('nnnnnn');
        $cmp = new Model\Component($req->getInt('component_id'));
        if (!$cmp->id) {
            return $res->buildBodyFromString('Compontent not found');
        }


        $tpl = __DIR__ . '/../View/draft/embedComponent.php';
        $view = $res->getView();
        $view->component = $cmp;
        $content = $view->getContent($tpl);
        exit($content);
        return $res->buildBodyFromString($content);
    }

    public function getJSONAction(HttpRequest $req, HttpResponse $res) {
        $cmp = new Model\Component($req->getInt('component_id'));
        if (!$cmp->id) {
            return $res->sendErrorMessageJSON('Compontent not found');
        }

        $obj = new \stdClass();
        $obj->success = TRUE;
        $obj->component = $cmp->toStdObject();

        return $res->sendJson(json_encode($obj));
    }
    public function updateImgTextAction(HttpRequest $req, HttpResponse $res){
        if (!$req->isAjax()) {
            return $res->sendErrorMessageJSON('Bad Request');
        }
        if (!$req->isPOST()) {
            return $res->sendErrorMessageJSON('Bad Request method');
        }
        if (!$draft_id = $req->getRouter('draft_id')) {
            return $res->sendErrorMessageJSON('Invalid Draft ID!');
        }
        if (!$component_id = $req->getInt('component_id')) {
            return $res->sendErrorMessageJSON('Invalid Component ID!');
        }
        $component = new Model\Component(array('id' => $component_id, 'draft_id' => $draft_id));
        if (!$component->id) {
            return $res->sendErrorMessageJSON('Component not found!');
        }
        try {
            if($text = $req->get('comment')){
               $component->comment = $text;
               $component->save();
           }
           $data= array('success'=>true,'component'=> $component->toStdObject());
           return $res->sendJson(json_encode($data));
        } catch (\Exception $exc) {
           return $res->sendErrorMessageJSON($res->getView()->renderException($exc));
        }
    }
    
    public function deleteAction(HttpRequest $req, HttpResponse $res) {
        if (!$req->isAjax()) {
            return $res->sendErrorMessageJSON('Bad Request');
        }
        if (!$req->isPOST()) {
            return $res->sendErrorMessageJSON('Bad Request method');
        }
        
        $component_id = $req->getRouter('component_id');
        
        $com = new Model\Component();
        $com->delete( array( 'id' => $component_id ) );
        
        return $res->sendSuccessMessageJSON('Component successfully deleted');
        
    }
    
    public function editAction(HttpRequest $req, HttpResponse $res) {
        if (!$req->isAjax()) {
            return $res->sendErrorMessageJSON('Bad Request');
        }
        if (!$req->isPOST()) {
            return $res->sendErrorMessageJSON('Bad Request method');
        }
        if (!$draft_id = $req->getRouter('draft_id')) {
            return $res->sendErrorMessageJSON('Invalid Draft ID!');
        }

        $cmpType = Model\ComponentType::findOne(array('id' => $req->getInt('type_id')));
        if (!$cmpType) {
            return $res->sendErrorMessageJSON('Invalid Component type!');
        }
        $component = new Model\Component(array('id' => $req->getInt('component_id'), 'draft_id' => $draft_id));
        if (!$component->id) {
            return $res->sendErrorMessageJSON('Component not found!');
        }

        try {
            $map = new \NZ\Map(array('component' => $component, 'draft_id' => $draft_id, 'componentType' => $cmpType));
            $component = $this->createOrEdit($map, $req, $res);
            $obj = new \stdClass();
            $obj->success = true;
            $obj->data = $component->toArray();
            $res->setViewVar('component', $component);
            $res->setViewVar('use_iFrame', TRUE);
            //$obj->html= $res->getViewContent('tpl/draft_component_content.php');
            $obj->html = $this->renderCode($component, $res, 'edit');
            return $res->sendJson(json_encode($obj));
        } catch (\Exception $exc) {
            return $res->sendErrorMessageJSON($exc->getMessage());
        }
    }

    private function createOrEdit(\NZ\Map $map, $req, $res) {
        $cmpType = $map->get('componentType');
        $draft_id = $map->get('draft_id');
        $component = $map->get('component');
        $validator = $cmpType->getValidator();
        $data = $validator->validate(new \NZ\Map(), $req);
        $component->comment = isset($data->comment) ? $data->comment : '';
        $component->code = isset($data->code) ? $data->code : '';
        $component->state = Model\Component::STATE_UNPUBLISHED;
        if (!$component->id) {
            $component->setCreatedAt();
            $component->user_id = $this->getUser()->id;
            $component->draft_id = $draft_id;
            $component->type_id = $cmpType->id;
            $component->order_nr = $component->getOrderNr($draft_id);
        }
        $component->save();
        return $component;
    }

}
