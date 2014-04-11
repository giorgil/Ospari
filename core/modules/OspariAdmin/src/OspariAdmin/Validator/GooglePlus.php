<?php
namespace OspariAdmin\Validator;

/**
 * Description of GooglePlus
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class GooglePlus extends Component{
    public function validate(HttpRequest $req) {
        return parent::validate($req);
    }
}
