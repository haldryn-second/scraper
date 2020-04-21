<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require "application/libraries/vendor/autoload.php";
use chriskacerguis\RestServer\RestController;

class API extends RestController {
    public function test_get(){
        $array=array("Hola", "Mundo");
         $this->response($array);
    }
   
    
}