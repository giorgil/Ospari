<?php
namespace OspariAdmin\Validator;

/**
 * Description of GooglePlus
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class GooglePlus extends Component{
    public function validate(\NZ\Map $map, HttpRequest $req, $type= NULL) {
        return parent::validate($map, $req,'google_plus_post');
    }
}
