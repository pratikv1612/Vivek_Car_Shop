<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Accessory Model (FULL FIXED VERSION)
 */
class Accessory_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /* =========================
     * LISTING
     * ========================= */
    public function get_list($filters = array(), $limit = 12, $offset = 0)
    {
        $this->db->select('accessories.*,
            accessory_categories.name as category_name,
            brands.name as brand_name,
            car_models.name as model_name');

        $this->db->from('accessories');

        $this->db->join('accessory_categories', 'accessory_categories.id = accessories.category_id', 'left');
        $this->db->join('brands', 'brands.id = accessories.brand_id', 'left');
        $this->db->join('car_models', 'car_models.id = accessories.model_id', 'left');

        $this->db->where('accessories.is_available', 1);

        if (!empty($filters['category_id'])) {
            $this->db->where('accessories.category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $this->db->where('accessories.brand_id', $filters['brand_id']);
        }

        if (!empty($filters['model_id'])) {
            $this->db->where('accessories.model_id', $filters['model_id']);
        }

        if (!empty($filters['min_price'])) {
            $this->db->where('accessories.price >=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $this->db->where('accessories.price <=', $filters['max_price']);
        }

        if (!empty($filters['keyword'])) {
            $this->db->group_start();
            $this->db->like('accessories.name', $filters['keyword']);
            $this->db->or_like('accessories.description', $filters['keyword']);
            $this->db->group_end();
        }

        if (!empty($filters['is_new'])) {
            $this->db->where('accessories.is_new', 1);
        }

        $this->db->order_by('accessories.display_order', 'ASC');
        $this->db->order_by('accessories.created_at', 'DESC');

        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /* =========================
     * COUNT
     * ========================= */
    public function count_list($filters = array())
    {
        $this->db->from('accessories');

        $this->db->where('is_available', 1);

        if (!empty($filters['category_id'])) {
            $this->db->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $this->db->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['model_id'])) {
            $this->db->where('model_id', $filters['model_id']);
        }

        if (!empty($filters['min_price'])) {
            $this->db->where('price >=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $this->db->where('price <=', $filters['max_price']);
        }

        if (!empty($filters['keyword'])) {
            $this->db->group_start();
            $this->db->like('name', $filters['keyword']);
            $this->db->or_like('description', $filters['keyword']);
            $this->db->group_end();
        }

        if (!empty($filters['is_new'])) {
            $this->db->where('is_new', 1);
        }

        return $this->db->count_all_results();
    }

    /* =========================
     * SINGLE ITEM
     * ========================= */
    public function get($id)
    {
        return $this->db->select('accessories.*,
                accessory_categories.name as category_name,
                brands.name as brand_name,
                car_models.name as model_name')
            ->from('accessories')
            ->join('accessory_categories', 'accessory_categories.id = accessories.category_id', 'left')
            ->join('brands', 'brands.id = accessories.brand_id', 'left')
            ->join('car_models', 'car_models.id = accessories.model_id', 'left')
            ->where('accessories.id', $id)
            ->get()
            ->row();
    }

    /* =========================
     * CRUD
     * ========================= */
    public function create($data)
    {
        $this->db->insert('accessories', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        return $this->db->where('id', $id)->update('accessories', $data);
    }

    public function delete($id)
    {
        // safe delete accessory images first
        $this->db->where('accessory_id', $id)->delete('accessory_images');

        return $this->db->where('id', $id)->delete('accessories');
    }

    /* =========================
     * FEATURED
     * ========================= */
    public function get_featured($limit = 6)
    {
        return $this->get_list([], $limit, 0);
    }

    public function get_related($accessory_id, $category_id, $limit = 6)
    {
        $this->db->select('accessories.*, accessory_categories.name as category_name')
            ->from('accessories')
            ->join('accessory_categories', 'accessory_categories.id = accessories.category_id', 'left')
            ->where('accessories.is_available', 1)
            ->where('accessories.id !=', $accessory_id);

        if ($category_id) {
            $this->db->where('accessories.category_id', $category_id);
        }

        return $this->db->order_by('accessories.is_new', 'DESC')
            ->order_by('accessories.display_order', 'ASC')
            ->limit($limit)
            ->get()
            ->result();
    }

    /* =========================
     * IMAGE FUNCTIONS (FIXED)
     * ========================= */

    // ADD IMAGE
    public function add_image($data)
    {
        return $this->db->insert('accessory_images', $data);
    }

    // GET IMAGES
    public function get_images($accessory_id)
    {
        return $this->db->where('accessory_id', $accessory_id)
            ->order_by('display_order', 'ASC')
            ->get('accessory_images')
            ->result();
    }

    // DELETE ALL IMAGES
    public function delete_images($accessory_id)
    {
        return $this->db->where('accessory_id', $accessory_id)
            ->delete('accessory_images');
    }
}
