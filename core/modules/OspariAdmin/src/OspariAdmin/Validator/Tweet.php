<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
