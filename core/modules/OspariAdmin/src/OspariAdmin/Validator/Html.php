<?php
namespace OspariAdmin\Validator;

/**
 * Description of Html
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Html extends Component{
    public function validate(HttpRequest $req) {
        return parent::validate($req,'html');
    }
}
