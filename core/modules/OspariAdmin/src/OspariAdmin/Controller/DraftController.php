<?php

/**
 * Description of DefaultController
 *
 * @author 28h.eu
 */

namespace OspariAdmin\Controller;

use NZ\HttpRequest;
use NZ\HttpResponse;
use OspariAdmin\Model;
use OspariAdmin\Model\Tag;
use OspariAdmin\Model\PostMeta;
use OspariAdmin\Validator\Text;

class DraftController extends BaseController {

    public function removeCoverAction(HttpRequest $req, HttpResponse $res) {
        try {
            $draft = new Model\Draft($req->getInt('draft_id'));
            $draft->cover = '';
            $draft->thumb = '';
            $draft->save();
            
            return $res->sendSuccessMessageJSON('+Done');
                
            
        } catch (\Exception $ex) {
            return $res->sendErrorMessageJSON( $ex->getMessage() );
        }
    }

    public function editComponentsAction(HttpRequest $req, HttpResponse $res) {
        $draft = new Model\Draft($req->getInt('draft_id'));
        $res->setViewVar('draft', $draft);
        $res->setViewVar('tags', Tag::getTagsAsString($draft->id));
        $res->setViewVar('cmpTypes', $this->buildCmpTypes());
        $res->setViewVar('hasPost', \Ospari\Model\Post::findOne(array('draft_id' => $draft->id)) ? TRUE : FALSE);
        $map = new \NZ\Map();
        $componentPager = Model\Component::getPager($map, $req);
        $res->setViewVar('components', $componentPager->getItems());
        $res->setViewVar('metaForm', $this->createMetaForm($req, $res->getView()));
        $res->buildBody('draft/edit_components.php');
    }

    public function editAction(HttpRequest $req, HttpResponse $res) {
        $draft = new Model\Draft($req->getInt('draft_id'));
        if (!$draft->id) {
            return $res->sendErrorMessage('Draft not found');
        }
        $view = $res->getView();
        $user = $this->getUser();
        $form = null;
        if ($req->isPOST()) {
            try {
                $form = $this->createForm($view, $req);
                $draft = $this->createOrEdit($form, $req, $user);
                $res->redirect('/' . OSPARI_ADMIN_PATH . '/draft/components/edit/' . $draft->id);
            } catch (\Exception $exc) {
                $res->setViewVar('Exception', $exc);
            }
        } else {

            $req = $draft->toHttpRequest($req);
            $form = $this->createForm($view, $req);
        }
        $res->setViewVar('form', $form);
        $res->setViewVar('draft', $draft);
        $res->setViewVar('req', $req);
        return $res->buildBody('draft/edit.php');
    }

    public function publishDraftAction(HttpRequest $req, HttpResponse $res) {
        if (!$req->isAjax()) {
            return $res->sendErrorMessage('Bad Request!');
        }

        if (!$req->isPOST()) {
            return $res->sendErrorMessage('Bad Request Method!');
        }

        try {
            $post = $this->pulishDraft($req);
            $std = new \stdClass();
            $std->url = $post->getUrl();
            $std->success = TRUE;
            $std->message = 'Draft successfully published';
            return $res->sendJson(json_encode($std));
        } catch (\Exception $exc) {
            return $res->sendErrorMessage($exc->getMessage());
        }
    }

    /**
     * 
     * @param type $req
     * @return \OspariAdmin\Model\Post
     * @throws \Exception
     */
    protected function pulishDraft($req) {
        $model = new \OspariAdmin\Model\Draft($req->getInt('draft_id'));
        if (!$model->id) {
            throw new \Exception('Draft not found!');
        }
        if ($req->state == \OspariAdmin\Model\Draft::STATE_PUBLISHED) {
            $model->state = \OspariAdmin\Model\Draft::STATE_PUBLISHED;
            $model->setDateTime('published_at', new \DateTime());
            $post = \OspariAdmin\Model\Post::findOne(array('draft_id' => $model->id));
            $modelArray = $model->toArray();
            unset($modelArray['id']);

            if (!$post) {
                $post = new \OspariAdmin\Model\Post();
                $post->draft_id = $model->id;
            }

            foreach ($modelArray as $k => $v) {
                $post->set($k, $v);
            }
            $post->save();
            $model->save();
            $sql = "UPDATE " . OSPARI_DB_PREFIX . "components SET state='" . Model\Component::STATE_PUBLISHED . "' WHERE draft_id='" . $model->id . "'";
            $model->query($sql);
            return $post;
            //return $res->sendSuccessMessageJSON('Draft successfully published');
        }
        throw new \Exception('something went wrong');
    }

    public function updateSlugAction(HttpRequest $req, HttpResponse $res) {
        $user = $this->getUser();
        if ($req->isPOST()) {
            try {
                $this->validateSlugData($req);
                $draft_id = $req->getInt('draft_id');
                $slug = $req->get('slug');
                $uri = new \NZ\Uri();
                $newSlug = $uri->slugify($slug);
                $draft = $this->slugExist($newSlug, $draft_id);
                if (!$user->canEditDraft($draft)) {
                    return $res->sendErrorMessageJSON('Access Denied!');
                }

                $draft->slug = $newSlug;
                $draft->setEditedAt(new \DateTime());
                $draft->save();
                $post = new \OspariAdmin\Model\Post(array('draft_id' => $draft->id));
                if ($post->id) {
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

        if ($req->isPOST()) {
            try {
                $draft = $this->createOrEdit($form, $req, $user);
                if ($draft) {
                    $res->redirect('/' . OSPARI_ADMIN_PATH . '/draft/components/edit/' . $draft->id);
                }
            } catch (\Exception $exc) {
                $res->setViewVar('Exception', $exc);
            }
        }

        $res->setViewVar('form', $form);
        $res->setViewVar('req', $req);
        $res->buildBody('draft/create.php');
    }

    public function unpublishAction(HttpRequest $req, HttpResponse $res) {
        if (!$req->isAjax()) {
            return $res->sendErrorMessage('Bad Request!');
        }

        if (!$req->isPOST()) {
            return $res->sendErrorMessage('Bad Request Method!');
        }
        $draft_id = $req->getRouter('draft_id');
        if (!$draft_id) {
            return $res->sendErrorMessageJSON('Invalid Identifier');
        }
        $post = \OspariAdmin\Model\Post::findOne(array('draft_id' => $draft_id));
        if ($post) {
            $post->delete(array('id' => $post->id));
            $draft = new \OspariAdmin\Model\Draft($draft_id);
            if ($draft->id) {
                $draft->state = \OspariAdmin\Model\Draft::STATE_UNPUBLISHED;
                $draft->save();
            }

            return $res->sendSuccessMessageJSON('ok');
        }
        return $res->sendErrorMessageJSON('Post not found!');
    }

    public function deleteAction(HttpRequest $req, HttpResponse $res) {
        if (!$req->isAjax()) {
            return $res->sendErrorMessage('Bad Request!');
        }

        if (!$req->isPOST()) {
            return $res->sendErrorMessage('Bad Request Method!');
        }

        $draft_id = $req->getRouter('draft_id');
        if (!$draft_id) {
            return $res->sendErrorMessageJSON('Invalid Identifier');
        }

        $model = \OspariAdmin\Model\Draft::findOne(array('id' => $draft_id));
        if (!$model) {
            return $res->sendErrorMessageJSON('Model not found!');
        }
        $this->deleteDraft2Media($draft_id);
        $this->deleteDraft2Tag($draft_id);

        $comp = new Model\Component();
        $comp->delete(array('draft_id' => $model->id));

        $model->delete(array('id' => $model->id));
        return $res->sendSuccessMessageJSON('ok');
    }

    public function metaAction(HttpRequest $req, HttpResponse $res) {
        if (!$req->isAjax()) {
            return $res->sendErrorMessage('Bad Request!');
        }
        if (!$req->isPOST()) {
            return $res->sendErrorMessage('Bad Request Method!');
        }
        $draft_id = $req->getRouter('draft_id');
        $form = $this->createMetaForm($req, $res->getView());
        $metas = $this->prepareMeta($req, $form);
        $this->saveMeta($metas, $draft_id);
        return $res->sendSuccessMessageJSON('ok');
    }

    private function createOrEdit(\NZ\BootstrapForm $form, $req, $user) {
        $this->validateForm($form, $req);
        $model = new \OspariAdmin\Model\Draft($req->getInt('draft_id'));
        if (!$model->id) {
            $model->setCreatedAt();
            $model->state = $req->getInt('state');
        }

        if (!$model->slug) {
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
        $validator = new Text();
        $obj = $validator->validate(new \NZ\Map(array('attr' => 'content')), $req);
        $model->content = $obj->content;

        $model->setDateTime('edited_at', new \DateTime());
        $model->save();


        return $model;
    }

    private function createSlug($slug, $try = 0) {
        if (!$slug) {
            $slug = 'post';
        }

        $draft = \OspariAdmin\Model\Draft::findOne(array('slug' => $slug));
        if ($draft) {
            $try++;
            $slug = $slug . '-' . $try;
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

    private function validateSlugData(\NZ\HttpRequest $req) {
        if (!$req->getInt('draft_id')) {
            throw new \Exception('Invalid Draft Identifier!');
        }
        if (!$req->get('slug')) {
            throw new \Exception('Invalid Slug!');
        }
    }

    private function slugExist($slug, $draft_id) {
        $post = \Ospari\Model\Post::findOne(array('slug' => $slug));
        if ($post && $post->draft_id != $draft_id) {
            throw new \Exception('This Slug already exist!');
        }
        $draft = \OspariAdmin\Model\Draft::findOne(array('slug' => $slug));
        if ($draft && $draft->id != $draft_id) {
            throw new \Exception('This Slug already exist!');
        }
        if ($draft) {
            return $draft;
        }
        $draft = \OspariAdmin\Model\Draft::findOne($draft_id);
        if (!$draft) {
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

        $form->createElement('content')
                ->toTexArea()
                ->setAttribute('rows', 10)
                ->setAttribute('autofocus', 'autofocus')
                ->setLabelText('Excerpt (Executive summary)')
                ->setAttribute('id', 'draft-content-textarea');
        $form->createHiddenElement('state', 'draft', 'post-state-input');

        return $form;
    }

    protected function deleteDraft2Media($draft_id) {
        $d2m = \OspariAdmin\Model\Draft2Media::findAll(array('draft_id' => $draft_id));
        foreach ($d2m as $item) {
            $item->delete();
        }
    }

    protected function deleteDraft2Tag($draft_id) {
        $d2m = \OspariAdmin\Model\Tag2Draft::findAll(array('draft_id' => $draft_id));
        foreach ($d2m as $item) {
            $item->delete();
        }
    }

    protected function saveMeta(array $metas, $draft_id) {
        foreach ($metas as $meta) {
            if ($meta['key_name'] && $meta['key_value']) {
                $model = new PostMeta(array('draft_id' => $draft_id, 'key_name' => $meta['key_name']));
                if (!$model->id) {
                    $model->key_name = $meta['key_name'];
                    $model->draft_id = $draft_id;
                }
                $model->key_value = $meta['key_value'];
                $model->save();
            }
        }
    }

    protected function prepareMeta(\NZ\HttpRequest $req, \NZ\BootstrapForm $form) {

        $arr = array();
        foreach ($form->getElements() as $el) {
            $name = $el->getName();
            $arr[] = array('key_name' => $name, 'key_value' => $req->get($name));
        }

        return $arr;
    }

    protected function createMetaForm(\NZ\HttpRequest $req, $view) {
        if (!$req->isPOST()) {
            $req = $this->setReqWithMetaData($req);
        }
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

    protected function setReqWithMetaData(\NZ\HttpRequest $req) {
        $draft_id = $req->getRouter('draft_id');
        $metas = PostMeta::findAll(array('draft_id' => $draft_id));
        foreach ($metas as $meta) {
            $req->set($meta->key_name, $meta->key_value);
        }
        return $req;
    }

    protected function buildCmpTypes() {
        $types = Model\ComponentType::findAll(array("1 ORDER by position ASC"));
        $arr = array();
        foreach ($types as $type) {
            $arr[$type->name] = $type->toArray();
        }
        return $arr;
    }

}
