<?php

namespace OspariAdmin\Validator;

/**
 * Description of Tweet
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
class Tweet extends Component{
    
    public function validate(\NZ\Map $map, HttpRequest $req, $type= NULL) {
        return parent::validate($map, $req,'tweet');
    }
}
