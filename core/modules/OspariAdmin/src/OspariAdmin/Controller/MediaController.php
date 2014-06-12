<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace OspariAdmin\Controller;

/**
 * Description of MediaController
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
use NZ\HttpResponse;
use OspariAdmin\Model\Media;
use OspariAdmin\Model\Draft2Media;
use OspariAdmin\Model\Draft;
use \NZ\Filehandler;
use \NZ\Image;
use \NZ\Uri;
use OspariAdmin\Model\Setting;

class MediaController extends BaseController {

    public function uploadAction(HttpRequest $req, HttpResponse $res) {
        $id = $req->getInt('draft_id');
        if (!$id) {
            return $res->sendErrorMessageJSON('Invalid Draft Identifier');
        }
        $draft = Draft::findOne(array('id' => $id));
        if (!$draft) {
            return $res->sendErrorMessageJSON('Draft could be found!');
        }
        $json = new \NZ\JsonView($res->getView());
        try {

            if ($req->hasUpload()) {
                $media = $this->handleUpload($req);
                $draft2media = new Draft2Media(array('draft_id' => $id, 'media_id' => $media->id));
                $this->saveDratf2Media($draft, $draft2media, $media);
                $where = array();
                $mode = 'add';
                if ($cmp_id = $req->getInt('component_id')) {
                    $where['draft_id'] = $draft->id;
                    $where['id'] = $cmp_id;
                    $mode = 'edit';
                }
                $cmp = new \OspariAdmin\Model\Component($where);
                $map = new \NZ\Map(array('media' => $media, 'draft' => $draft));
                $this->saveCmp($map, $cmp, $req);
                $obj = new \stdClass();
                $obj->success = true;
                $obj->data = $cmp->toArray();
                $res->setViewVar('component', $cmp);
                $res->setViewVar('use_iFrame', false);
                $obj->mode = $mode;
                $obj->html = $this->renderCode($cmp, $res, $mode);

                return $res->sendJson(json_encode($obj));
            } else {
                return $res->sendErrorMessageJSON('No Upload found');
            }
        } catch (\Exception $exc) {
            echo $exc;
            return $res->sendErrorMessageJSON($exc->getMessage());
        }
    }

    protected function renderCode($component, $res, $mode = 'add') {
        if ($mode == 'edit') {
            $res->setViewVar('hasHandle', true);
            return $res->getViewContent('tpl/draft_component_content.php');
        }
        return $res->getViewContent('tpl/draft_component.php');
    }

    private function saveDratf2Media($draft, $draft2media, $media) {
        if (!$draft2media->id) {
            $draft2media->draft_id = $draft->id;
            $draft2media->media_id = $media->id;
            $draft2media->save();
        }
        $this->updateDraft($draft, $media);
    }

    private function updateDraft($draft, $media) {
        $draft->thumb = $media->thumb;
        $draft->media_id = $media->id;
        $draft->save();
    }

    private function saveCmp(\NZ\Map $map, $cmp, $req) {
        $purifier = new \HTMLPurifier();
        $media = $map->media;
        $draft = $map->draft;
        $cmp->comment = $purifier->purify($req->get('comment'));
        $cmp->code = $media->large;
        if (!$cmp->id) {
            $cmp->user_id = $this->getUser()->id;
            $cmp->draft_id = $draft->id;
            $cmp->type_id = $req->getInt('type_id');
            $cmp->setCreatedAt();
            $counter = new \OspariAdmin\Model\Component();
            $count = $counter->count(array('draft_id' => $draft->id));
            $cmp->order_nr = $count + 1;
        }
        $cmp->save();
    }

    public function handleUpload(HttpRequest $req) {
        $fh = new Filehandler();
        $setting = new Setting();
        $subPath = '/' . date('Y') . '/' . date('m');
        $path = '/content/upload' . $subPath;
        $absolute_path = $_SERVER['DOCUMENT_ROOT'] . $path;
        $fh->makeDirs($absolute_path);

        $src = Image::tryUpload('image');
        $img = new Image($src);
        $uri = new Uri();
        $name = $_FILES['image']['name'];

        $slug = $uri->slugify($this->removeExtension($name));

        $original = $subPath . '/' . $slug . '.' . $img->getExtension();
        $img->save($absolute_path . '/' . $slug . '.' . $img->getExtension());

        $w = $setting->get('img_width') ? $setting->get('img_width') : '600';
        $h = $setting->get('img_height') ? $setting->get('img_height') : '450';
        $img->scale($w, $h);
        $img->save($absolute_path . '/' . $slug . '-' . $w . 'x' . $h . '.' . $img->getExtension());
        $large = $subPath . '/' . $slug . '-' . $w . 'x' . $h . '.' . $img->getExtension();


        $w = $setting->get('thumb_width') ? $setting->get('thumb_width') : '160';
        $h = $setting->get('thumb_height') ? $setting->get('thumb_height') : '160';
        $img->scale($w, $h);
        $img->save($absolute_path . '/' . $slug . '-' . $w . 'x' . $h . '.' . $img->getExtension());
        $thumb = $subPath . '/' . $slug . '-' . $w . 'x' . $h . '.' . $img->getExtension();


        $media = new Media(array('large' => $large, 'user_id' => $this->getUser()->id));

        if (!$media->id) {
            $media->setCreatedAt();
            $media->user_id = $this->getUser()->id;
        }

        $media->original = $original;
        $media->large = $large;
        $media->thumb = $thumb;
        $media->ext = $img->getExtension();
        $media->save();
        return $media;
    }

    private function removeExtension($filename) {
        $arr = explode('.', $filename);
        if (count($arr) > 1) {
            array_pop($arr);
        }
        $str = implode('', $arr);
        return $str;
    }
}
