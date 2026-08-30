<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Accessories Controller
 */
class Accessories extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Accessory_model');
        $this->load->model('Accessory_image_model');
        $this->load->model('Accessory_category_model');
        $this->load->model('Brand_model');
        $this->load->model('Car_model_model');
    }

    public function index($page = 1) {
        $this->catalog($page);
    }

    public function new_arrivals($page = 1) {
        $this->catalog($page, true);
    }

    private function catalog($page = 1, $new_arrivals_only = false) {
        $per_page = 12;
        $filters = array(
            'category_id' => $this->input->get('category'),
            'brand_id' => $this->input->get('brand'),
            'model_id' => $this->input->get('model'),
            'min_price' => $this->input->get('min_price'),
            'max_price' => $this->input->get('max_price'),
            'keyword' => $this->input->get('keyword'),
            'is_new' => $new_arrivals_only ? 1 : null,
        );
        
        $total = $this->Accessory_model->count_list($filters);
        $offset = ($page - 1) * $per_page;
        
        $this->data['accessories'] = $this->Accessory_model->get_list($filters, $per_page, $offset);
        foreach ($this->data['accessories'] as $a) {
            $p = $this->Accessory_image_model->get_primary($a->id);
            $a->primary_image = $p ? base_url($p->image_path) : base_url('assets/images/placeholder-product.svg');
        }
        $this->data['filters'] = $filters;
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = ceil($total / $per_page);
        $this->data['accessory_categories'] = $this->Accessory_category_model->get_all();
        $this->data['categories'] = $this->data['accessory_categories'];
        $this->data['catalog_title'] = $new_arrivals_only ? 'New Arrivals' : 'Shop';
        $this->data['catalog_base_url'] = $new_arrivals_only ? 'new-arrivals' : 'accessories';
        $this->data['meta_title'] = ($new_arrivals_only ? 'New Arrivals' : 'Car Accessories') . ' - ' . $this->data['site_name'];
        
        if (!empty($filters['brand_id'])) {
            $this->data['models'] = $this->Car_model_model->get_by_brand($filters['brand_id']);
        } else {
            $this->data['models'] = array();
        }
        
        $this->load->view('layout/header', $this->data);
        $this->load->view('accessories/listing', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function detail($id, $slug = '') {
        $this->load->helper('text');
        $accessory = $this->Accessory_model->get($id);
        if (!$accessory || !$accessory->is_available) {
            show_404();
        }
        
        $this->data['accessory'] = $accessory;
        $this->data['images'] = $this->Accessory_image_model->get_by_accessory($id);
        $this->data['whatsapp_url'] = whatsapp_accessory_url($accessory->name);
        $this->data['related_accessories'] = $this->Accessory_model->get_related($accessory->id, $accessory->category_id, 6);
        foreach ($this->data['related_accessories'] as $related) {
            $primary_image = $this->Accessory_image_model->get_primary($related->id);
            $related->primary_image = $primary_image ? base_url($primary_image->image_path) : '';
        }
        $this->data['meta_title'] = $accessory->name . ' - ' . $this->data['site_name'];
        
        $this->load->view('layout/header', $this->data);
        $this->load->view('accessories/detail', $this->data);
        $this->load->view('layout/footer', $this->data);
    }
}
