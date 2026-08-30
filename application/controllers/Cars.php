<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cars Controller - Listing and details
 */
class Cars extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Car_model');
        $this->load->model('Car_image_model');
        $this->load->model('Car_model_model');
        $this->load->model('Fuel_type_model');
        $this->load->model('Transmission_model');
        $this->load->model('Saved_car_model');
    }

    public function index($page = 1) {
        $per_page = 12;
        $filters = array(
            'brand_id' => $this->input->get('brand'),
            'model_id' => $this->input->get('model'),
            'fuel_type_id' => $this->input->get('fuel'),
            'city_id' => $this->input->get('city'),
            'min_price' => $this->input->get('min_price'),
            'max_price' => $this->input->get('max_price'),
            'min_year' => $this->input->get('min_year'),
            'max_year' => $this->input->get('max_year'),
            'keyword' => $this->input->get('keyword'),
        );
        $filters = array_filter($filters, function($v) { return $v !== '' && $v !== NULL; });
        $order_by = $this->input->get('sort') ?: 'newest';
        
        $total = $this->Car_model->count_list($filters);
        $offset = ($page - 1) * $per_page;
        
        $this->data['cars'] = $this->Car_model->get_list($filters, $per_page, $offset, $order_by);
        $this->data['filters'] = $filters;
        $this->data['order_by'] = $order_by;
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = ceil($total / $per_page);
        $this->data['fuel_types'] = $this->Fuel_type_model->get_all();
        $this->data['transmissions'] = $this->Transmission_model->get_all();
        $this->data['meta_title'] = 'Browse Cars - ' . $this->data['site_name'];
        
        // Models for selected brand
        if (!empty($filters['brand_id'])) {
            $this->data['models'] = $this->Car_model_model->get_by_brand($filters['brand_id']);
        } else {
            $this->data['models'] = array();
        }
        
        foreach ($this->data['cars'] as $c) {
            $p = $this->Car_image_model->get_primary($c->id);
            $c->primary_image = $p ? base_url($p->image_path) : base_url('assets/images/placeholder-car.svg');
        }
        
        $this->load->view('layout/header', $this->data);
        $this->load->view('cars/listing', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function models_by_brand() {
        header('Content-Type: application/json');
        $brand_id = (int) $this->input->get('brand_id');
        $models = $this->Car_model_model->get_by_brand($brand_id);
        echo json_encode($models);
    }

    public function detail($id, $slug = '') {
        $this->load->helper('text');
        $car = $this->Car_model->get($id);
        if (!$car || $car->status != 'approved') {
            show_404();
        }
        
        $this->Car_model->increment_view($id);
        
        $this->data['car'] = $car;
        $this->data['images'] = $this->Car_image_model->get_by_car($id);
        $this->data['car_name'] = $car->brand_name . ' ' . $car->model_name . ($car->variant ? ' ' . $car->variant : '');
        $this->data['whatsapp_url'] = whatsapp_car_url($this->data['car_name']);
        
        if ($this->session->userdata('user_id')) {
            $this->data['is_saved'] = $this->Saved_car_model->is_saved($this->session->userdata('user_id'), $id);
        } else {
            $this->data['is_saved'] = FALSE;
        }
        
        $this->data['meta_title'] = $this->data['car_name'] . ' - ' . $this->data['site_name'];
        $this->data['meta_description'] = character_limiter(strip_tags($car->description), 160);
        $this->load->view('layout/header', $this->data);
        $this->load->view('cars/detail', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function toggle_save() {
        header('Content-Type: application/json');
        $car_id = (int) $this->input->post('car_id');
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            echo json_encode(array('status' => FALSE, 'message' => 'Please login to save vehicles.'));
            return;
        }
        $saved = $this->Saved_car_model->is_saved($user_id, $car_id);
        if ($saved) {
            $this->Saved_car_model->remove($user_id, $car_id);
            $now_saved = FALSE;
        } else {
            $this->Saved_car_model->add($user_id, $car_id);
            $now_saved = TRUE;
        }
        echo json_encode(array('status' => TRUE, 'saved' => $now_saved));
    }
}
