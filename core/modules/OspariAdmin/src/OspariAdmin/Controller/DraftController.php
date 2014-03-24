<?php


/**
 * Description of DefaultController
 *
 * @author 28h.eu
 */

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;
use OspariAdmin\Model\Tag;
use OspariAdmin\Model\PostMeta;

class DraftController extends BaseController {

    public function editAction(HttpRequest $req, HttpResponse $res) {
         return $this->createAction($req, $res);
    }
    public function updateSlugAction( HttpRequest $req, HttpResponse $res ){
         $user = $this->getUser();
         if($req->isPOST()){
             try {
                 $this->validateSlugData($req);
                 $draft_id = $req->getInt('draft_id');
                 $slug = $req->get('slug');
                 $uri = new \NZ\Uri();
                 $newSlug = $uri->slugify($slug);
                 $draft = $this->slugExist( $newSlug, $draft_id );
                 if(!$user->canEditDraft($draft)){
                    return $res->sendErrorMessageJSON('Access Denied!');
                 }
                 
                 $draft->slug = $newSlug;
                 $draft->setEditedAt(new \DateTime());
                 $draft->save();
                 $post = new \OspariAdmin\Model\Post(array('draft_id'=>$draft->id));
                 if($post->id){
                        $post->slug = $newSlug;
                        $post->setEditedAt(new \DateTime());
                        $post->save();
                 }
               } catch (\Exception $exc) {
                        return $res->sendErrorMessageJSON($exc->getMessage());
               }
             return $res->sendSuccessMessageJSON($newSlug);
             
         }
    }

    public function createAction(HttpRequest $req, HttpResponse $res) {

        $view = $res->getView();
        $user = $this->getUser();
        $form = $this->createForm($view, $req);
        $jsonView = new \NZ\JsonView($view);

        if ($req->isPOST()) {
            try {
                $draft = $this->createOrEdit($form, $req, $user);

                $jsonView->set('draft_id', $draft->id);
                $jsonView->set('draft_url', $draft->getUrl());
                if ($draft->isPublished()) {
                    $jsonView->setSuccessMessage('Awsome! Your post has been published');
                     $jsonView->set('post_url', $draft->getUrl());
                    $jsonView->set('published', 1);
                }else{
                    $jsonView->setSuccessMessage('Your post has been saved as draft.');
                }
                
                return $res->sendJson($jsonView->render());
                
            } catch (\Exception $exc) {
                return $res->sendErrorMessageJSON($exc->__toString());
                //$res->setViewVar('Exception', $exc);
            }
        }
        
        if( $draft_id = $req->getInt('draft_id') ){
            $draft = new \OspariAdmin\Model\Draft( $draft_id );
            $req = $draft->toHttpRequest($req);
            $req->set('tags', Tag::getTagsAsString($draft_id));
            $form = $this->createForm($view, $req);
        }
        
        

        $res->setViewVar('uploadURL', OSPARI_URL.'/'.OSPARI_ADMIN_PATH.'/media/upload');
        $res->setViewVar('req', $req);
        $res->setViewVar('form', $form);
        $res->setViewVar('metaForm', $this->createMetaForm($req, $view));
        $res->buildBody('draft/create.php');
    }
    
    public function unpublishAction(HttpRequest $req, HttpResponse $res){
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request!');
        }
        
        if(!$req->isPOST()){
            return $res->sendErrorMessage('Bad Request Method!');
        }
        $draft_id = $req->getRouter('draft_id');
        if(!$draft_id){
            return $res->sendErrorMessageJSON('Invalid Identifier');
        }
        $post = \OspariAdmin\Model\Post::findOne(array('draft_id'=>$draft_id));
        if($post){
            $post->delete();
            $draft = new \OspariAdmin\Model\Draft($draft_id);
            if($draft->id){
                $draft->state = \OspariAdmin\Model\Draft::STATE_UNPUBLISHED;
                $draft->save();
            }
            
            return $res->sendSuccessMessageJSON('ok');
        }
        return $res->sendErrorMessageJSON('Post not found!');
    }
    
    public function deleteAction(HttpRequest $req, HttpResponse $res){
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request!');
        }
        
        if(!$req->isPOST()){
            return $res->sendErrorMessage('Bad Request Method!');
        }
        
        $draft_id = $req->getRouter('draft_id');
        if(!$draft_id){
            return $res->sendErrorMessageJSON('Invalid Identifier');
        }
        $post = \OspariAdmin\Model\Post::findOne(array('draft_id'=>$draft_id));
        if($post){
            $post->delete();
        }
        $model = \OspariAdmin\Model\Draft::findOne(array('id'=>$draft_id));
        if(!$model){
            return $res->sendErrorMessageJSON('Model not found!');
        }
        $this->deleteDraft2Media($draft_id);
        $this->deleteDraft2Tag($draft_id);
        return $res->sendSuccessMessageJSON('ok');

    }
    public function metaFormAction(HttpRequest $req, HttpResponse $res){
        
    }

    public function metaAction(HttpRequest $req, HttpResponse $res){
        if(!$req->isAjax()){
            return $res->sendErrorMessage('Bad Request!');
        }
        if(!$req->isPOST()){
            return $res->sendErrorMessage('Bad Request Method!');
        }
        $draft_id = $req->getRouter('draft_id');
        $form = $this->createMetaForm($req, $res->getView());
        $metas = $this->prepareMeta($req, $form);
        $this->saveMeta($metas, $draft_id);
        return $res->sendSuccessMessageJSON('ok');
    }

    public function autoSaveAction(HttpRequest $req, HttpResponse $res) {
        $view = $res->getView();
        $user = $this->getUser();
        $form = $this->createForm($view, $req);

        $jsonView = new \NZ\JsonView($view);

        if ($req->isPOST()) {
            try {
                $draft = $this->createOrEdit($form, $req, $user);

                $jsonView->setSuccessMessage('Auto saved on.' . $draft->edited_at . '.');
                $jsonView->set('draft_id', $draft->id);
                $jsonView->set('draft_slug', $draft->slug);
                $jsonView->set('draft_url', $draft->getUrl());

                return $res->sendJson($jsonView->render());
            } catch (\Exception $exc) {
                return $res->sendErrorMessageJSON($exc->__toString());

                return $res->sendErrorMessageJSON($exc->getMessage());
            }
        } else {
            $res->sendErrorMessageJSON('Post method required');
        }
    }


    private function createOrEdit(\NZ\BootstrapForm $form, $req, $user) {


        $model = new \OspariAdmin\Model\Draft($req->getInt('draft_id'));

        if (!$model->id) {
            $model->setCreatedAt();
            $model->state = $req->getInt('state');
        }
        
         if (!$model->slug ) {
            $nzUri = new \NZ\Uri();
            $slug = $nzUri->slugify($req->title);
            
            $model->slug = $this->createSlug($slug);
          }

        if ($req->state == \OspariAdmin\Model\Draft::STATE_PUBLISHED) {
            $model->state = \OspariAdmin\Model\Draft::STATE_PUBLISHED;
            $model->setDateTime('published_at', new \DateTime());
        }

        //$model = $form->saveToModel($model);
        $model->title = $req->title;
        $model->user_id = $user->id;
        $model->content = $req->content;
        $model->code = $req->code;
        //$model->tags = $req->tags;
        $model->setDateTime('edited_at', new \DateTime());
        $model->save();

        if ($req->state == \OspariAdmin\Model\Draft::STATE_PUBLISHED) {
            
            $post = \OspariAdmin\Model\Post::findOne( array('draft_id' => $model->id ) );
            $modelArray = $model->toArray();
            unset($modelArray['id']);
            unset($modelArray['code']);
            if( !$post ){
                $post = new \OspariAdmin\Model\Post();
                $post->draft_id = $model->id;
            }

            foreach ($modelArray as $k => $v) {
                $post->set($k, $v);
            }
            $post->save();
           
        }
        return $model;
    }

    
    private function createSlug( $slug, $try = 0 ){
        if( !$slug ){
            $slug = 'post';
        }
        
        $draft = \OspariAdmin\Model\Draft::findOne( array( 'slug' => $slug ) );
        if( $draft ){
            $try++;
            $slug = $slug.'-'.$try;
            return $this->createSlug($slug, $try);
        }
        return $slug;
        
        
    }

    private function validateForm(\NZ\BootstrapForm $form, \NZ\HttpRequest $req) {
        if (!$form->validate($req)) {
            throw new \Exception('Please fill all required fields');
        }

        return TRUE;
    }
    private function validateSlugData( \NZ\HttpRequest $req ){
        if( !$req->getInt('draft_id') ){
            throw new \Exception('Invalid Draft Identifier!');
        }
        if(!$req->get('slug')){
            throw new \Exception('Invalid Slug!');
        }
    }
    private function slugExist($slug, $draft_id){
         $post = \Ospari\Model\Post::findOne(array('slug'=>$slug));
         if($post && $post->draft_id != $draft_id){
             throw new \Exception('This Slug already exist!');
         }
         $draft = \OspariAdmin\Model\Draft::findOne(array('slug'=>$slug));
         if($draft && $draft->id != $draft_id){
             throw new \Exception('This Slug already exist!');
         }
         if($draft){
             return $draft;
         }
         $draft = \OspariAdmin\Model\Draft::findOne($draft_id);
         if(!$draft){
             throw new \Exception('Draft could not be found!');
         }
         return $draft;
        
    }

    private function createForm($view, \NZ\HttpRequest $req) {
        $form = new \NZ\BootstrapForm($view, $req);
        $form->setCssClass('form-horizontal');
        $form->createElement('title')
                ->setAttribute('placeholder', 'title')
                ->setRequired();
        
        $form->createElement('code')
                ->toTexArea()
                ->setAttribute('rows', 10)
                ->setAttribute('autofocus', 'autofocus')
                ->setHelpText('Type "![]()" to upload photos.')
                ->setAttribute('id', 'draft-content-textarea');

         
         $form->createElement('cover')
                ->setAttribute('placeholder', 'Cover Image') ;
                
        
        $form->createElement('tags')
                ->setAttribute('autocomplete', 'off')
                ->setAttribute('placeholder', 'Type something and hit enter')
                ->setAttribute('id', 'tag-input')
                ->setRequired();
        $form->createHiddenElement('state', 'draft', 'post-state-input');
        return $form;
    }
    
    protected function deleteDraft2Media($draft_id){
        $d2m = \OspariAdmin\Model\Draft2Media::findAll(array('draft_id'=>$draft_id));
        foreach($d2m as $item){
            $item->delete();
        }
    }
    
    protected function deleteDraft2Tag($draft_id){
        $d2m = \OspariAdmin\Model\Tag2Draft::findAll(array('draft_id'=>$draft_id));
        foreach($d2m as $item){
            $item->delete();
        }
    }
    
    protected function saveMeta(array $metas, $draft_id){
        foreach ($metas as $meta){
            if($meta['key_name'] && $meta['key_value']){
                $model = new PostMeta(array('draft_id'=>$draft_id,'key_name'=>$meta['key_name']));
                if(!$model->id){
                    $model->key_value = $meta['key_value'];
                    $model->key_name = $meta['key_name'];
                    $model->draft_id = $draft_id;
                    $model->save();
                }
            }
        }
    }
    protected function prepareMeta(\NZ\HttpRequest $req, \NZ\BootstrapForm $form){
        
        $arr =array();
        foreach ($form->getElements() as $el){
            $name = $el->getName();
            $arr[] =array('key_name'=>$name, 'key_value'=>$req->get($name)) ;
        }
        
        return $arr;
    }
    protected function createMetaForm(\NZ\HttpRequest $req , $view){
        $form = new \NZ\BootstrapForm($view, $req);
        $form->createElement('meta-title')
                ->setLabelText('Title')
                ->setType('text');
        $form->createElement('meta-keyword')
                ->setLabelText('Keywords')
                ->setType('text');
        $form->createElement('meta-description')
                ->setLabelText('Description')
                ->toTexArea();
        $form->addCssClass('form-horizontal');
        
        return $form;
    }
}
