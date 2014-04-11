<?php

namespace OspariAdmin\Validator;

/**
 * Description of Youtube
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Youtube extends Component{
    public function validate(\NZ\Map $map, HttpRequest $req,$type=NULL) {
        parent::validate($map, $req, 'youtube_video');
    }
}
