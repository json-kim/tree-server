<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';

class Memory extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Memory_model');
        $this->load->model('Member_model');
        $this->load->model('Tree_model');
    }

    /**
     * 메모리 리스트(최신순)
     * @method: GET
     * @link: api/memory/list
     */
    public function list_get()
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


        $page = $this->input->get('page'); // 요청 페이지
        $total_count = $this->Memory_model->total_count("", "", "")->count; // 메모리 전체 개수
        $page_size = 20; // 페이지 사이즈
        $max_page = intval($total_count / $page_size) + 1; // 총 페이지

        if ($page > $max_page || $page < 1) {
            // 최대 페이지 넘기거나, 1보다 작은 페이지의 경우
            $data['error'] = 'page must be less than '.$max_page;
            $this->response($data, 400);
        } else {
            $start = ($page - 1) * $page_size; // 시작 위치

            $result = $this->Memory_model->select_post_list($start, $page_size);
            
            $data['total_count'] = intval($total_count);
            $data['total_page'] = $max_page;
            $data['current_page'] = intval($page);
            $data['result'] = $result;
    
            $this->response($data, 200);
        }
    }

    /**
     * 수종별 메모리 리스트
     * @method: GET
     * @link: api/memory/wood_list
     */
    public function wood_list_get()
    {
        // ********************************************
        // 토큰 유효 체크
        $header = $this->input->request_headers();
        if (!isset($header['Authorization'])) {
            // 헤더가 잘못되었을 경우
            $this->response(['message' => 'invalid_request'], 400);
            return;
        }

        $token = explode(' ', $header['Authorization'])[1];$token = $header['Authorization'];

        $valid_check = $this->Member_model->isValidToken($token);

        if (!$valid_check) {
            // 토큰이 유효하지 않으면
            $this->response(['message' => 'invalid token'], 401);
            return;
        }
        // ********************************************


        $wood_name = $this->input->get('wood_name'); // 수종 이름
        $page = $this->input->get('page'); // 요청 페이지
        $total_count = $this->Memory_model->total_count($wood_name, "", "")->count; // 메모리 전체 개수
        $page_size = 20; // 페이지 사이즈
        $max_page = intval($total_count / $page_size) + 1; // 총 페이지

        if ($page > $max_page || $page < 1) {
            // 최대 페이지 넘기거나, 1보다 작은 페이지의 경우
            $data['error'] = 'page must be less than '.$max_page;
            $this->response($data, 400);
        } else {
            $start = ($page - 1) * $page_size; // 시작 위치

            $result = $this->Memory_model->select_wood_post_list($start, $page_size, $wood_name);
            
            $data['total_count'] = intval($total_count);
            $data['total_page'] = $max_page;
            $data['current_page'] = intval($page);
            $data['result'] = $result;
    
            $this->response($data, 200);
        }
    }

    /**
     * 구별 메모리 리스트
     * @method: GET
     * @link: api/memory/gu_list
     */
    public function gu_list_get()
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


        $gu_name = $this->input->get('gu_name'); // 구 이름
        $page = $this->input->get('page'); // 요청 페이지
        $total_count = $this->Memory_model->total_count("", $gu_name, "")->count; // 메모리 전체 개수
        $page_size = 20; // 페이지 사이즈
        $max_page = intval($total_count / $page_size) + 1; // 총 페이지

        if ($page > $max_page || $page < 1) {
            // 최대 페이지 넘기거나, 1보다 작은 페이지의 경우
            $data['error'] = 'page must be less than '.$max_page;
            $this->response($data, 400);
        } else {
            $start = ($page - 1) * $page_size; // 시작 위치

            $result = $this->Memory_model->select_gu_post_list($start, $page_size, $gu_name);
            
            $data['total_count'] = $total_count;
            $data['total_page'] = $max_page;
            $data['current_page'] = intval($page);
            $data['result'] = $result;
    
            $this->response($data, 200);
        }
    }

    /**
     * 테마별 메모리 리스트
     * @method: GET
     * @link: api/memory/theme_list
     */
    public function theme_list_get()
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


        $theme_name = $this->input->get('theme'); // 요청 테마
        $page = $this->input->get('page'); // 요청 페이지
        $total_count = $this->Memory_model->total_count("", "", $theme_name)->count; // 메모리 전체 개수
        $page_size = 20; // 페이지 사이즈
        $max_page = intval($total_count / $page_size) + 1; // 총 페이지

        if ($page > $max_page || $page < 1) {
            // 최대 페이지 넘기거나, 1보다 작은 페이지의 경우
            $data['error'] = 'page must be less than '.$max_page;
            $this->response($data, 400);
        } else {
            $start = ($page - 1) * $page_size; // 시작 위치

            $result = $this->Memory_model->select_theme_post_list($start, $page_size, $theme_name);
    
            $data['total_count'] = $total_count;
            $data['total_page'] = $max_page;
            $data['current_page'] = intval($page);
            $data['result'] = $result;
    
            $this->response($data, 200);
        }
    }

    /**
     * 메모리 추가
     * @param:
     * content(String)
     * member_id(int)
     * wood_name(String)
     * theme_id(int)
     * private(int) 0~1
     * ------------------------
     * @method: POST
     * @link: api/memory/insert
     */
    public function insert_post()
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


        $this->form_validation->set_rules('content', 'Content', 'required'); // 컨텐츠
        $this->form_validation->set_rules('member_id', 'Member', 'required'); // 멤버id
        $this->form_validation->set_rules('wood_name', 'WoodName', 'required'); // 선택 수종
        $this->form_validation->set_rules('theme_id', 'Theme', 'required|is_natural_no_zero'); // 테마
        $this->form_validation->set_rules('private', 'Private', 'required|in_list[0,1]'); // 공개여부(0은 공개)

        if ($this->form_validation->run() == FALSE) 
        {
            $message = array(
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_error()
            );

            $this->response($message, REST_Controller::HTTP_NOT_FOUND);
        }
        else
        {
            $content = $this->input->post('content', TRUE);
            $member_id = $this->input->post('member_id', TRUE);
            $wood_name = $this->input->post('wood_name', TRUE);
            $theme_id = $this->input->post('theme_id', TRUE);
            $private = $this->input->post('private', TRUE);

            $tree_id = $this->Tree_model->select_random_tree($wood_name);

            // 해당종류의 나무가 없다면 에러 처리
            if (!isset($tree_id->_id)) {
                $message = array(
                    'message' => 'select other tree'
                );
    
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            } else {
                $input_data = [
                    'content' => $content,
                    'member_id' => $member_id,
                    'tree_id' => $tree_id->_id,
                    'theme_id' => $theme_id,
                    'private' => $private
                ];

                $result = $this->Memory_model->insert_post($input_data);

                if ($result > 0 AND !empty($result)) {
                    $this->response(['message' => 'posting registration success'], 200);
                } else {
                    $this->response(['message' => 'posting registration fail'], REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
    }

    public function test_get() {
        $text = 'bearer dskjflkjsdlfkjdslfkjdsfl';
        var_dump(explode(' ', $text)[1]);
    }
}
