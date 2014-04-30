<?php

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;
use \OspariAdmin\Model;

Class MediaLibController extends BaseController{
    
     public function listAction( HttpRequest $req, HttpResponse $res ){
         
         $nzmap = new \NZ\Map();
         $pager = Model\Media::getPager($nzmap, $req);
         
         $res->setViewVar('items', $pager->getItems());
         $res->setViewVar('draft_id', $req->getInt('draft_id') );
         $res->buildBody('/media/listMedia.php');
         
     }
     
     public function setCoverAction( HttpRequest $req, HttpResponse $res ){
         
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
     
     
    
}
