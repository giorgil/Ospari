<?php
namespace OspariAdmin\Validator;

/**
 * Description of Text
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Text extends Component{
    public function validate(\NZ\Map $map, HttpRequest $req ,$type=NULL) {
        return parent::validate($map, $req,'text');
    }
}
