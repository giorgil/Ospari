<?php
namespace OspariAdmin\Validator;

/**
 * Description of Facebook
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Facebook extends Component{
    public function validate(HttpRequest $req) {
        return parent::validate($req);
    }
}
