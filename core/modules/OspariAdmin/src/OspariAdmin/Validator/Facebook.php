<?php
namespace OspariAdmin\Validator;

/**
 * Description of Facebook
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Facebook extends Component{
    public function validate(\NZ\Map $map, HttpRequest $req, $type= NULL) {
        return parent::validate($map, $req,'facebook_post');
    }
}
