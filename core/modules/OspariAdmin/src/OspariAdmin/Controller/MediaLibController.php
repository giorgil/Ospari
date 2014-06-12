<?php

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;
use \OspariAdmin\Model;
use OspariAdmin\Controller\MediaController;

Class MediaLibController extends BaseController{
    
     public function listAction( HttpRequest $req, HttpResponse $res ){
       
         $nzmap = new \NZ\Map();
         $pager = Model\Media::getPager($nzmap, $req);
         
         $res->setViewVar('items', $pager->getItems());
           $res->setViewVar('req', $req );
       
         $res->buildBody('/media/listMedia.php');
         
     }
     
     public function setUserImageAction( HttpRequest $req, HttpResponse $res ){
         
         if( !$req->isPOST() ){
             return $res->sendErrorMessageJSON('Bad request method');
         }
         
         $media_id = $req->getInt('media_id');
         
         $media = new Model\Media( $media_id );
         if( !$media->id ){
             return $res->sendErrorMessageJSON('Cover image not found');
         }
         
         $u = new Model\User();
         $u->image = $media->getThumb();
         $u->update( array('id' => $req->getInt('user')) );
         
         //$res->redirect( $draft->getEditUrl() );
         $std = new \stdClass();
         $std->sucess = TRUE;
         $std->url = '/'.OSPARI_ADMIN_PATH.'/user';
         $json = json_encode($std);
         $res->sendJson($json);
         
         
     }
     
     public function setUserCoverAction( HttpRequest $req, HttpResponse $res ){
         
         if( !$req->isPOST() ){
             return $res->sendErrorMessageJSON('Bad request method');
         }
         
         $media_id = $req->getInt('media_id');
         
         $media = new Model\Media( $media_id );
         if( !$media->id ){
             return $res->sendErrorMessageJSON('Cover image not found');
         }
         
         $u = new Model\User();
         $u->cover = $media->getLarge();
         $u->update( array('id' => $req->getInt('user')) );
         
         //$res->redirect( $draft->getEditUrl() );
         $std = new \stdClass();
         $std->sucess = TRUE;
         $std->url = '/'.OSPARI_ADMIN_PATH.'/user';
         $json = json_encode($std);
         $res->sendJson($json);
         
         
     }
     
     public function setBlogCoverAction( HttpRequest $req, HttpResponse $res ){
         
         if( !$req->isPOST() ){
             return $res->sendErrorMessageJSON('Bad request method');
         }
         
         $media_id = $req->getInt('media_id');
         
         $media = new Model\Media( $media_id );
         if( !$media->id ){
             return $res->sendErrorMessageJSON('Cover image not found');
         }
         
         $setting = new Model\Setting();
         $setting->cover = $media->getLarge();
         $setting->save();
         
         //$res->redirect( $draft->getEditUrl() );
         $std = new \stdClass();
         $std->sucess = TRUE;
         $std->url = '/'.OSPARI_ADMIN_PATH.'/setting';
         $json = json_encode($std);
         $res->sendJson($json);
         
         
     }
     
     public function setBlogLogoAction( HttpRequest $req, HttpResponse $res ){
         
         if( !$req->isPOST() ){
             return $res->sendErrorMessageJSON('Bad request method');
         }
         
         $media_id = $req->getInt('media_id');
         
         $media = new Model\Media( $media_id );
         if( !$media->id ){
             return $res->sendErrorMessageJSON('Cover image not found');
         }
         
         $setting = new Model\Setting();
         $setting->logo = $media->getThumb();
         $setting->save();
         
         //$res->redirect( $draft->getEditUrl() );
         $std = new \stdClass();
         $std->sucess = TRUE;
         $std->url = '/'.OSPARI_ADMIN_PATH.'/setting';
         $json = json_encode($std);
         $res->sendJson($json);
         
         
     }
     
     
     public function setDraftCoverAction( HttpRequest $req, HttpResponse $res ){
         
         if( !$req->isPOST() ){
             return $res->sendErrorMessageJSON('Bad request method');
         }
         
         
         $draft_id = $req->getInt('draft_id');
         $media_id = $req->getInt('media_id');
         
         $media = new Model\Media( $media_id );
         $draft = new Model\Draft($draft_id);
         
         $draft->cover = $media->getLarge();
         $draft->thumb = $media->getThumb();
         $draft->save();
         
         //$res->redirect( $draft->getEditUrl() );
         $std = new \stdClass();
         $std->sucess = TRUE;
         $std->url = $draft->getEditUrl();
         $json = json_encode($std);
         $res->sendJson($json);
         
         
     }
     public function uploadAction(HttpRequest $req, HttpResponse $res){
         
         if ($req->hasUpload()) {
            $mediaCtrl = new MediaController($req, $res);
            try {
                $media = $mediaCtrl->handleUpload($req);
                $res->setViewVar('item', $media);
                 $res->setViewVar('draft_id', $req->getInt('draft_id'));
                $json=array('success'=>true,'html'=>$res->getViewContent('media/media_item.php'));
                return $res->sendJson(json_encode($json));
            } catch (\Exception $exc) {
                return $res->sendErrorMessageJSON($res->getView()->renderException($exc));
            }
         }
         return $res->sendErrorMessageJSON('No file uploaded!');
     }
     
     
    
}
