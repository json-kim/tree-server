<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

class Memory extends RestController {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Memory_model');
    }

    /**
     * 메모리 리스트(최신순)
     * @method: GET
     * @link: api/memory/list
     */
    public function list_get()
    {
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
     * @method: POST
     * @link: api/memory/insert
     */
    public function insert_post()
    {

    }
}
