<?php

namespace OspariAdmin\Validator;

/**
 * Description of Tweet
 *
 * @author 28h
 */
use NZ\HttpRequest;
class Instagramm extends Component{
    
    public function validate(\NZ\Map $map, HttpRequest $req, $type= NULL) {
        return parent::validate($map, $req,'tweet');
    }
}