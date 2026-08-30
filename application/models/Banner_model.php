<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Banner Model - Homepage banners
 */
class Banner_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_active($placement = null) {
        $this->db->where('is_active', 1);
        if ($placement !== null) {
            $this->db->where('placement', $placement);
        }

        return $this->db
            ->order_by('display_order', 'ASC')
            ->get('banners')
            ->result();
    }

    public function get_all() {
        return $this->db->order_by('display_order', 'ASC')->get('banners')->result();
    }

    public function get($id) {
        return $this->db->where('id', $id)->get('banners')->row();
    }

    public function create($data) {
        $this->db->insert('banners', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('banners', $data);
    }

    public function delete($id) {
        return $this->db->where('id', $id)->delete('banners');
    }
}
