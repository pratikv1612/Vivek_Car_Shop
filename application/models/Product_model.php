<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->ensure_product_columns();
    }

    public function get_list($filters = array(), $limit = 20, $offset = 0) {
        $this->base_query();

        if (!empty($filters['status'])) {
            $this->db->where('accessories.product_status', $filters['status']);
        }

        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $this->db->where('accessories.is_featured', (int) $filters['is_featured']);
        }

        if (isset($filters['is_new']) && $filters['is_new'] !== '') {
            $this->db->where('accessories.is_new', (int) $filters['is_new']);
        }

        if (isset($filters['is_hot_sold']) && $filters['is_hot_sold'] !== '') {
            $this->db->where('accessories.is_hot_sold', (int) $filters['is_hot_sold']);
        }

        if (!empty($filters['category_id'])) {
            $this->db->where('accessories.category_id', $filters['category_id']);
        }

        $this->db->order_by('accessories.display_order', 'ASC');
        $this->db->order_by('accessories.created_at', 'DESC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    public function count_list($filters = array()) {
        $this->db->from('accessories');

        if (!empty($filters['status'])) {
            $this->db->where('product_status', $filters['status']);
        }

        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $this->db->where('is_featured', (int) $filters['is_featured']);
        }

        if (isset($filters['is_new']) && $filters['is_new'] !== '') {
            $this->db->where('is_new', (int) $filters['is_new']);
        }

        if (isset($filters['is_hot_sold']) && $filters['is_hot_sold'] !== '') {
            $this->db->where('is_hot_sold', (int) $filters['is_hot_sold']);
        }

        if (!empty($filters['category_id'])) {
            $this->db->where('category_id', $filters['category_id']);
        }

        return $this->db->count_all_results();
    }

    public function get_featured($limit = 8) {
        return $this->get_list(['status' => 'available', 'is_featured' => 1], $limit, 0);
    }

    public function get_new($limit = 18) {
        return $this->get_list(['status' => 'available', 'is_new' => 1], $limit, 0);
    }

    public function get_sold($limit = 8) {
        return $this->get_list(['status' => 'sold'], $limit, 0);
    }

    public function get_hot_deals($limit = 2) {
        return $this->get_list(['status' => 'available', 'is_featured' => 1], $limit, 0);
    }

    public function get($id) {
        $this->base_query();
        return $this->db->where('accessories.id', $id)->get()->row();
    }

    public function create($data) {
        $this->db->insert('accessories', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update('accessories', $data);
    }

    public function delete($id) {
        $this->db->where('accessory_id', $id)->delete('accessory_images');
        return $this->db->where('id', $id)->delete('accessories');
    }

    private function base_query() {
        $this->db->select('accessories.*,
            accessory_categories.name as category_name,
            brands.name as brand_name,
            car_models.name as model_name');
        $this->db->from('accessories');
        $this->db->join('accessory_categories', 'accessory_categories.id = accessories.category_id', 'left');
        $this->db->join('brands', 'brands.id = accessories.brand_id', 'left');
        $this->db->join('car_models', 'car_models.id = accessories.model_id', 'left');
    }

    private function ensure_product_columns() {
        if (!$this->db->field_exists('is_featured', 'accessories')) {
            $this->db->query("ALTER TABLE accessories ADD is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER is_available");
        }

        if (!$this->db->field_exists('is_new', 'accessories')) {
            $this->db->query("ALTER TABLE accessories ADD is_new TINYINT(1) NOT NULL DEFAULT 1 AFTER is_featured");
        }

        if (!$this->db->field_exists('product_status', 'accessories')) {
            $this->db->query("ALTER TABLE accessories ADD product_status VARCHAR(20) NOT NULL DEFAULT 'available' AFTER is_new");
        }

        if (!$this->db->field_exists('is_hot_sold', 'accessories')) {
            $this->db->query("ALTER TABLE accessories ADD is_hot_sold TINYINT(1) NOT NULL DEFAULT 0 AFTER is_featured");
        }
    }

    public function get_hot_sold($limit = 8) {
        return $this->get_list(['status' => 'available', 'is_hot_sold' => 1], $limit, 0);
    }
}
