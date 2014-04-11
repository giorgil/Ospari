<?php
namespace OspariAdmin\Validator;

/**
 * Description of Html
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Html extends Component{
    public function validate(\NZ\Map $map, HttpRequest $req, $type=NULL) {
        return parent::validate($map, $req,'html');
    }
}
