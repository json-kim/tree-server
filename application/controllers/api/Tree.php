<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

use \Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';

class Tree extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Tree_model');
        $this->load->model('Member_model');
    }

    /**
     * 나무 리스트
     * @method: GET
     * @link: api/tree/tree_list
     */
    public function tree_list_get()
    {
        // ********************************************
        // 토큰 유효 체크
        $header = $this->input->request_headers();
        if (!isset($header['Authorization'])) {
            // 헤더가 잘못되었을 경우
            $this->response(['message' => 'invalid_request'], 400);
            return;
        }

        $token = explode(' ', $header['Authorization'])[1];

        $valid_check = $this->Member_model->isValidToken($token);

        if (!$valid_check) {
            // 토큰이 유효하지 않으면
            $this->response(['message' => 'invalid token'], 401);
            return;
        }
        // ********************************************


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
        // ********************************************
        // 토큰 유효 체크
        $header = $this->input->request_headers();
        if (!isset($header['Authorization'])) {
            // 헤더가 잘못되었을 경우
            $this->response(['message' => 'invalid_request'], 400);
            return;
        }

        $token = explode(' ', $header['Authorization'])[1];

        $valid_check = $this->Member_model->isValidToken($token);

        if (!$valid_check) {
            // 토큰이 유효하지 않으면
            $this->response(['message' => 'invalid token'], 401);
            return;
        }
        // ********************************************


        $count = $this->Tree_model->count_tree();
        $match_count = $this->Tree_model->count_match_tree();
        $wood_count = $this->Tree_model->count_group_tree();

        $data['count'] = $count->count;
        $data['match_count'] = $match_count->count;
        $data['wood_count'] = $wood_count;
        $this->response($data, 200);
    }
}
