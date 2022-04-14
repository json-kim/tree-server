<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Tree extends RestController {

    public function __construct()
    {
        parent::__construct();
    }

    public function tree_list_get()
    {
        header("Access-Control-Allow-Origin: *");

        $trees = [
            ['id' => 0, 'name' => 'John', 'email' => 'john@example.com'],
            ['id' => 1, 'name' => 'Jim', 'email' => 'jim@example.com'],
        ];
        
        $this->response($trees, 200);
    }

}
