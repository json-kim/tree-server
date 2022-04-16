<?php
class Tree_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }

    // 나무 리스트 가져오기
    public function select_tree_list() {
    
        $data = $this->db->query("
        select
            *
        from
            tree
        LIMIT
            20
        ");

        return $data->result_array();
    }

    // 나무 개수 가져오기
    public function count_tree() {
        $data = $this->db->query("
        select
            count(*) as count
        from
            tree
        ");

        return $data->row();
        
    }

    // 등록된 나무의 개수
    public function count_match_tree() {
        $data = $this->db->query("
        select
            count(*) as count
        from
            tree
        where
            status = 1
        ");

        return $data->row();
    }

    // 수종별 나무의 개수
    public function count_group_tree() {
        $data = $this->db->query("
        SELECT
            wood_name,
            COUNT(*) as count
        FROM
            tree
        GROUP BY
            wood_name
        ");

        return $data->result_array();
    }

    // 랜덤 나무 반환
    public function select_random_tree($wood_name) {
        $data = $this->db->query("
        SELECT
            _id
        FROM
            tree
        WHERE
            wood_name = '$wood_name'
        AND
            status = 0
        ORDER BY
            RAND()
        LIMIT 1
        ;
        ");

        return $data->row();
    }
}

