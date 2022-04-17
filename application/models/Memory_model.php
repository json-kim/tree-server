<?php
class Memory_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 메모리 개수 가져오기
    public function total_count($wood_name, $gu_name, $theme_name) {
        if ($wood_name != '') {
            $and_query = "
            AND tree.wood_name = '$wood_name'
            ";
        } else if ($gu_name != '') {
            $and_query = "
            AND tree.gu_name = '$gu_name'
            ";
        } else if ($theme_name != '') {
            $and_query = "
            AND theme.name = '$theme_name'
            ";
        } else {
            $and_query = "";
        }

        $data = $this->db->query("
        SELECT
            count(*) as count
        FROM
            memory
        JOIN
            tree
        ON
            memory.tree_id = tree._id
        JOIN
            theme
        ON
            memory.theme_id = theme._id
        WHERE
            memory.status = 0
        ".$and_query);

        return $data->row();
    }

    // 메모리 리스트 가져오기
    public function select_post_list($start, $size) {
        $data = $this->db->query("
        SELECT 
            memory._id,
            memory.content,
            memory.created_at,
            memory.tree_id,
            memory.member_id,
            tree.lat,
            tree.lng,
            tree.gu_name,
            tree.street_name,
            tree.wood_name,
            member.name,
            theme.name as theme
        FROM
            memory
        JOIN
            tree
        ON
            memory.tree_id = tree._id
        JOIN
            member
        ON
            memory.member_id = member._id
        JOIN
            theme
        ON
            memory.theme_id = theme._id
        WHERE
            memory.status = 0
        ORDER BY
            memory.created_at
        LIMIT
            $start, $size
        ;
        ");

        return $data->result_array();
    }

    // 수종별 메모리 리스트 가져오기
    public function select_wood_post_list($start, $size, $wood_name) {
        $data = $this->db->query("
        SELECT 
            memory._id,
            memory.content,
            memory.created_at,
            memory.tree_id,
            memory.member_id,
            tree.lat,
            tree.lng,
            tree.gu_name,
            tree.street_name,
            tree.wood_name,
            member.name,
            theme.name as theme
        FROM
            memory
        JOIN
            tree
        ON
            memory.tree_id = tree._id
        JOIN
            member
        ON
            memory.member_id = member._id
        JOIN
            theme
        ON
            memory.theme_id = theme._id
        WHERE
            memory.status = 0
        AND
            tree.wood_name = '$wood_name'
        ORDER BY
            memory.created_at
        LIMIT
            $start, $size
        ;
        ");

        return $data->result_array();
    }

    // 구별 메모리 리스트 가져오기
    public function select_gu_post_list($start, $size, $gu_name) {
        $data = $this->db->query("
        SELECT 
            memory._id,
            memory.content,
            memory.created_at,
            memory.tree_id,
            memory.member_id,
            tree.lat,
            tree.lng,
            tree.gu_name,
            tree.street_name,
            tree.wood_name,
            member.name,
            theme.name as theme
        FROM
            memory
        JOIN
            tree
        ON
            memory.tree_id = tree._id
        JOIN
            member
        ON
            memory.member_id = member._id
        JOIN
            theme
        ON
            memory.theme_id = theme._id
        WHERE
            memory.status = 0
        AND
            tree.gu_name = '$gu_name'
        ORDER BY
            memory.created_at
        LIMIT
            $start, $size
        ;
        ");

        return $data->result_array();
    }

    // 테마별 메모리 리스트 가져오기
    public function select_theme_post_list($start, $size, $theme_name) {
        $data = $this->db->query("
        SELECT 
            memory._id,
            memory.content,
            memory.created_at,
            memory.tree_id,
            memory.member_id,
            tree.lat,
            tree.lng,
            tree.gu_name,
            tree.street_name,
            tree.wood_name,
            member.name,
            theme.name as theme
        FROM
            memory
        JOIN
            tree
        ON
            memory.tree_id = tree._id
        JOIN
            member
        ON
            memory.member_id = member._id
        JOIN
            theme
        ON
            memory.theme_id = theme._id
        WHERE
            memory.status = 0
        AND
            theme.name = '$theme_name'
        ORDER BY
            memory.created_at
        LIMIT
            $start, $size
        ;
        ");

        return $data->result_array();
    }

    // 새로운 포스팅 추가
    // tree 테이블에 입력 트리 id와 일치하는 값의 status를 1로 업데이트
    // 업데이트에 실패하면 롤백 시키고 0 리턴
    // 업데이트 성공시 memory 테이블에 값을 입력하고 새로 입력된 값의 id를 리턴
    public function insert_post(array $data) {
        $this->db->query("START TRANSACTION");

        $update_result = $this->db->query("
        UPDATE tree
        SET
            status = 1
        WHERE
            _id = ".$data['tree_id']."
        ");
        
        if (!$update_result || $this->db->affected_rows() != 1) {
            $this->db->query("ROLLBACK");

            return 0;
        } else {
            $result = $this->db->query("
            INSERT INTO memory
                (content, tree_id, member_id, theme_id, private)
            VALUES
                ('".$data['content']."', ".$data['tree_id'].", ".$data['member_id'].", ".$data['theme_id'].", ".$data['private'].")
            ");
            $insert_id = $this->db->insert_id();

            if (!$result) {
                // 실패시
                $this->db->query("ROLLBACK");

                return 0;
            } else {
                // 성공시
                $this->db->query("COMMIT");

                return $insert_id;
            }
        }
    }
}

