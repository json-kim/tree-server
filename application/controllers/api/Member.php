<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

use \Restserver\Libraries\REST_Controller;

require APPPATH . 'libraries/REST_Controller.php';

class Member extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('Member_model');
    }

    /**
     * 회원 가입 메서드
     * @method: POST
     * @link: api/member/insert
     */
    public function insert_post() {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[member.email]', 
            array('is_unique' => 'This %s already exists please enter another email')); // 이메일
        $this->form_validation->set_rules('pw', 'Password', 'trim|required'); // 비밀번호
        $this->form_validation->set_rules('name', 'Name', 'trim|required|is_unique[member.name]', 
            array('is_unique' => 'This %s already exists please enter another member name')); // 이름

        if ($this->form_validation->run() == FALSE) {
            $message = array(
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors()
            );

            $this->response($message, 401);
        } else {
            $insert_data = [
                'email' => $this->input->post('email', TRUE),
                'pw' => $this->input->post('pw', TRUE),
                'name' => $this->input->post('name', TRUE)
            ];

            // 멤버 삽입 메서드
            $result = $this->Member_model->member_insert($insert_data);

            if ($result > 0 AND !empty($result)) {
                $this->response([
                    'message' => 'sign up success'
                ], 200);
            } else {
                $this->response([
                    'message' => 'sign up fail'
                ], 401);
            }
        }
    }

    /**
     * 로그인 메서드
     * @method: POST
     * @link: api/member/login
     */
    public function login_post() {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email', 
            array('valid_email' => 'This %s is not valid email')); // 이메일
        $this->form_validation->set_rules('pw', 'Password', 'trim|required'); // 비밀번호

        if ($this->form_validation->run() == FALSE) {
            $message = array(
                'status' => false,
                'error' => $this->form_validation->error_array(),
                'message' => validation_errors()
            );

            $this->response($message, 401);
        } else {
            $login_data = [
                'email' => $this->input->post('email', TRUE),
                'pw' => $this->input->post('pw', TRUE)
            ];

            $result = $this->Member_model->member_login($login_data);

            if (isset($result->_id)) {
                $token_result = $this->Member_model->create_token($result->_id);

                if ($result == 'error') {
                    $this->response(['message' => 'login fail'], 401);
                } else {
                    $token_data['token_type'] = 'bearer';
                    $token_data['access_token'] = $token_result['access_token'];
                    $token_data['expires_in'] = $token_result['expires_in'];
                    $token_data['refresh_token'] = $token_result['refresh_token'];

                    $this->response(['message' => 'login success', 'response' => $token_result], 200);
                }
            } else {
                $this->response(['message' => 'login fail'], 401);
            }
        }
    }

    /**
     * 로그아웃 요청 메서드
     * @method: POST
     * @link: api/member/logout
     */
    public function logout_post() {
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

        // 토큰이 유효하면
        $this->Member_model->deleteToken($token);
        $this->response(['message' => 'logout success'], 200);
    }

    /**
     * 토큰 갱신 요청 메서드
     * @method: POST
     * @link: api/member/refresh
     */
    public function refresh_post() {
        $refresh_token = $this->input->post('refresh_token', TRUE);

        $token_result = $this->Member_model->refresh_access_token($refresh_token);

        if ($token_result['status']) {
            $token_data = [
                'token_type' => 'bearer',
                'access_token' => $token_result['access_token'],
                'expires_in' => $token_result['expires_in']
            ];
            // 갱신 성공시
            $this->response([
                'message' => 'token refresh success',
                'response' => $token_data  
            ], 200);
        } else {
            // 갱신 실패시
            $this->response([
                'message' => 'invalid_token'
            ], 401);
        }
    }
}
