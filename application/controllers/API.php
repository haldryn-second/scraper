<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

class API extends REST_Controller {
    public function test_get(){
        $array=array("Hola", "Mundo");
         $this->response($array);
    }    
}