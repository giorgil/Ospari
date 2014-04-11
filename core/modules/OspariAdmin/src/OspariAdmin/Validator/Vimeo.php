<?php

namespace OspariAdmin\Validator;

/**
 * Description of Vimeo
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Vimeo extends Component{
    public function validate(\NZ\Map $map, HttpRequest $req,$type=NULL) {
        return parent::validate($map, $req, 'vimeo_video');
    }
}
