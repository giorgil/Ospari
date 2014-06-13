<?php

namespace OspariAdmin\Controller;
use NZ\HttpRequest;
use NZ\HttpResponse;
class BaseController {
    
    protected $user;
    
    public function __construct( HttpRequest $req, HttpResponse $res ){
        $this->user = new \OspariAdmin\Model\User( $req->getSession()->user_id );
        //var_dump($_SESSION);exit(1);
        //$this->user = new \OspariAdmin\Model\User(1);
    }
    
    public function getUser(){
        return $this->user;
    }
    
    public function indexAction( HttpRequest $req, HttpResponse $res ){
        
        $setting = new \OspariAdmin\Model\Setting();
        if( $setting->ospari_version != OSPARI_VERSION ){
            return $res->redirect( OSPARI_ADMIN_PATH.'/update'  );
        }
        
        
        
        $postsPager = new \NZ\Pager(new \OspariAdmin\Model\Post(), array('state'=>  \OspariAdmin\Model\Post::STATE_PUBLISHED), 1, 10,array('view_count'=>'DESC'));
        $res->setViewVar('mostViewedPosts', $postsPager->getItems());
        $res->setViewVar('isWritable', $this->isUploadFolderWritable());
        $res->buildBody('index.php');
    }
    
    public function listAction(HttpRequest $req, HttpResponse $res ){
        $user = $this->getUser();
        $map = new \NZ\Map();
        $map->user_id = $user->id;
        $map->like = $req->get('query');
        $pager = \OspariAdmin\Model\Draft::getPager($map, $req,20);
        $res->setViewVar('draftPager', $pager);
        $content = $res->getViewContent('tpl/drafts.php');
        $json = new \NZ\JsonView($res->getView());
        $json->setHtml($content);
        $json->set('success', true);
        return $res->sendJson($json->render());
    }

    public function onPageNotFound( HttpRequest $req, HttpResponse $res ){
        $res->setStatusCode(404);
          $view = $res->getView();
          $body = $view->getPartialContent(__DIR__.'/../View/404.php');
        $res->buildBodyFromString($body);
    }
    
    private function isUploadFolderWritable(){
        $path = '/content/upload';
        $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        if(is_writable($absolute_path)){
            return true;
        }
        return false;
    }
    
}