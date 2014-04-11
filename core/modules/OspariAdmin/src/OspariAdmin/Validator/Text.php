<?php
namespace OspariAdmin\Validator;

/**
 * Description of Text
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Text extends Component{
    public function validate(HttpRequest $req ) {
        return parent::validate($req,'text');
    }
}
