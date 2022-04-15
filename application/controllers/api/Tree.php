<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Tree extends RestController {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tree_model');
    }

    /**
     * 나무 리스트
     * @method: GET
     * @link: api/tree/tree_list
     */
    public function tree_list_get()
    {
        $trees = $this->Tree_model->select_tree_list();
        
        $this->response($trees, 200);
    }


    /**
     * 나무 개수(총 개수, 등록된 개수, 수종별 개수)
     * @method: GET
     * @link: api/tree/count
     */
    public function count_get()
    {
        $count = $this->Tree_model->count_tree();
        $match_count = $this->Tree_model->count_match_tree();
        $wood_count = $this->Tree_model->count_group_tree();

        $data['count'] = $count->count;
        $data['match_count'] = $match_count->count;
        $data['wood_count'] = $wood_count;
        $this->response($data, 200);
    }

    /**
     * 
     */
    public function insert_post()
    {

    }
}
