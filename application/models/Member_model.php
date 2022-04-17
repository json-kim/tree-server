<?php
class Member_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 새로운 회원 추가 메서드
    public function member_insert(array $data) {
        $this->db->query("
        INSERT INTO member
            (email, pw, name)
        VALUES
            ('".$data['email']."', '".$data['pw']."', '".$data['name']."')
        ;
        ");

        return $this->db->insert_id();
    }

    // 로그인 메서드
    public function member_login(array $data) {
        $result = $this->db->query("
        SELECT
            _id
        FROM
            member
        WHERE
            email = '".$data['email']."'
        AND
            pw = '".$data['pw']."'
        ;
        ");

        return $result->row();
    }

    // 토큰 발급 메서드
    // 이미 저장된 토큰이 있다면 업데이트
    // 없다면 새로 발급
    public function create_token($member_id) {
        $access_token = '';
        $refresh_token = '';
        $count = 0;
        $expires_in;
        
        while(TRUE) {
            // 랜덤 토큰 생성(54글자)
            $char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY_-";
            $access_token = '';
            for ($i = 0; $i < 54; $i++) $access_token .= $char[(rand() % strlen($char))];
            $refresh_token = '';
            for ($i = 0; $i < 54; $i++) $refresh_token .= $char[(rand() % strlen($char))];
            $current_time = time();
            $expires_in = $current_time + 43200;

            // db에 토큰 있는지 확인
            $check_result = $this->db->query("
            SELECT
                _id
            FROM
                api_token
            WHERE
                member_id = $member_id
            ;
            ");

            if (isset($check_result->row()->_id)) {
                // 이미 저장된 토큰이 있다면
                // db에 토큰 업데이트

                $query = "
                UPDATE api_token
                SET
                    access_token = '$access_token',
                    expires_in = FROM_UNIXTIME($expires_in),
                    refresh_token = '$refresh_token'
                WHERE
                    member_id = $member_id
                ;
                ";

                $this->db->db_debug = false;

                if(!@$this->db->query($query))
                {
                    // 에러시
                    $count += 1;
                    if ($count > 100) {
                        return 'error';
                    }
                }else{
                    // 성공시
                    break;
                }
            } else {
                // 저장된 토큰이 없다면
                // 토큰 db에 삽입
                $query = "
                INSERT INTO api_token
                    (access_token, expires_in, refresh_token, member_id)
                VALUES
                    ('$access_token', FROM_UNIXTIME($expires_in), '$refresh_token', $member_id)
                ;
                ";

                $this->db->db_debug = false;

                if(!@$this->db->query($query))
                {
                    // 에러시
                    $count += 1;
                    if ($count > 100) {
                        return 'error';
                    }
                }else{
                    // 성공시
                    break;
                }
            }
        }

        return [
            'access_token' => $access_token,
            'expires_in' => $expires_in,
            'refresh_token' => $refresh_token
        ];
    }

    // 로그아웃시 토큰 폐지
    public function deleteToken($access_token) {
        $this->db->query("
        DELETE FROM api_token
        WHERE
            access_token = '$access_token'
        ;
        ");
    }

    // 토큰 유효 체크
    public function isValidToken($access_token) {
        $result = $this->db->query("
        SELECT
            *,
            UNIX_TIMESTAMP(expires_in) as time_stamp
        FROM
            api_token
        WHERE
            access_token = '$access_token'
        ;
        ");

        if (isset($result->row()->_id)) {
            // 토큰이 존재한다면
            if ($result->row()->time_stamp < time()) {
                // 토큰이 만료되면
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            // 토큰이 없다면
            return FALSE;
        }
    }

    /**
     * 액세스 토큰 갱신
     * @param: refresh_token
     * @return: [access_token, expires_in]
     */
    public function refresh_access_token($refresh_token) {
        $char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY_-";
        $access_token = '';
        for ($i = 0; $i < 54; $i++) $access_token .= $char[(rand() % strlen($char))];
        $current_time = time();
        $expires_in = $current_time + 43200;

        $this->db->query("
        UPDATE api_token
        SET
            access_token = '$access_token',
            expires_in = FROM_UNIXTIME($expires_in)
        WHERE
            refresh_token = '$refresh_token'
        ;
        ");

        if ($this->db->affected_rows() < 1) {
            return [
                'status' => FALSE
            ];
        } else {
            return [
                'status' => TRUE,
                'access_token' => $access_token,
                'expires_in' => $expires_in
            ];
        }
    }
}

