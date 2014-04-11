<?php
namespace OspariAdmin\Validator;

/**
 * Description of Component
 *
 * @author fon-pah
 */
use NZ\HttpRequest;
use HTMLPurifier;
class Component {
    public function validate(HttpRequest $req, $type= NULL ){
        if($type=='text'){
            return $this->validateText($req);
        }
        
        if($type == 'html'){
           return $this->validateHtml($req); 
        }
        return $this->ValidateAll($req);
    }
    
    protected function validateText(HttpRequest$req){
        if(!$comment = $req->get('comment')){
            throw new \Exception('Comment required');
        }
        $obj = new \stdClass();
        $purifier = new HTMLPurifier();
        $obj->comment = $purifier->purify($comment);
        
        return $obj;
        
    }
    protected function validateHtml(HttpRequest$req){
        if(!$code = $req->get('code')){
            throw new \Exception('Code required');
        }
        $obj = new \stdClass();
        $obj->code = $code;
        return $obj;
    }
    
    protected function ValidateAll(HttpRequest$req){
        if(!$comment = $req->get('comment')){
            throw new \Exception('Comment required');
        }
        if(!$code = $req->get('code')){
            throw new \Exception('Code required');
        }
        $obj = new \stdClass();
        $obj->code = $this->removeScript($code);
        $purifier = new HTMLPurifier();
        $obj->comment = $purifier->purify($comment);
        return $obj;
    }
    
   protected function removeScript($string){
        $dom = new \DOMDocument();
        $dom->loadHTML($string);
        foreach ( $dom->getElementsByTagName("script") as $node) {
            $node->parentNode->removeChild($node);
        }
        $body = $dom->getElementsByTagName('body')->item(0);
        $code = $dom->saveHTML($body);
        //without the script twitter pictures are no loaded
        return str_replace(array('<body>','</body>'), '',$code);
    }
}
