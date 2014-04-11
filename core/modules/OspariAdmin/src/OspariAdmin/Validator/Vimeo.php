<?php

namespace OspariAdmin\Validator;

/**
 * Description of Vimeo
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Vimeo extends Component{
    public function validate(HttpRequest $req) {
        return parent::validate($req);
    }
}
