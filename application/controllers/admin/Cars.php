<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cars extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('Car_model');
        $this->load->model('Car_image_model');
        $this->load->model('Brand_model');
        $this->load->model('Car_model_model');
        $this->load->model('City_model');
        $this->load->model('Fuel_type_model');
        $this->load->model('Transmission_model');

        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }

    // ================= INDEX =================
    public function index($page = 1) {

        $per_page = 20;

        $filters = [
            'status'   => $this->input->get('status'),
            'brand_id' => $this->input->get('brand'),
        ];

        $total  = $this->Car_model->count_admin_list($filters);
        $offset = ($page - 1) * $per_page;

        $this->data['cars'] = $this->Car_model->get_admin_list($filters, $per_page, $offset);
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = ceil($total / $per_page);
        $this->data['entity'] = 'cars';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/list', $this->data);
        $this->load->view('admin/layout/footer');
    }

    // ================= CREATE VIEW =================
    public function create() {

        $this->data['brands'] = $this->Brand_model->get_all(FALSE);
        $this->data['cities'] = $this->City_model->get_all(FALSE);
        $this->data['fuel_types'] = $this->Fuel_type_model->get_all();
        $this->data['transmissions'] = $this->Transmission_model->get_all();
        $this->data['models'] = [];

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/cars/create', $this->data);
        $this->load->view('admin/layout/footer');
    }

    // ================= STORE (AJAX) =================
    public function store() {

        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('brand_id', 'Brand', 'required');
        $this->form_validation->set_rules('model_id', 'Model', 'required');
        $this->form_validation->set_rules('year', 'Year', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required');

        if (!$this->form_validation->run()) {
            echo json_encode([
                'status' => false,
                'errors' => validation_errors()
            ]);
            return;
        }

        $data = [
            'brand_id' => $this->input->post('brand_id'),
            'model_id' => $this->input->post('model_id'),
            'variant' => $this->input->post('variant', TRUE),
            'year' => $this->input->post('year'),
            'fuel_type_id' => $this->input->post('fuel_type_id'),
            'transmission_id' => $this->input->post('transmission_id'),
            'mileage' => $this->input->post('mileage', TRUE),
            'price' => $this->input->post('price'),
            'city_id' => $this->input->post('city_id'),
            'description' => $this->input->post('description', TRUE),
            'seller_name' => $this->input->post('seller_name', TRUE),
            'seller_phone' => $this->input->post('seller_phone', TRUE),
            'seller_email' => $this->input->post('seller_email', TRUE),
            'status' => $this->input->post('status') ?: 'approved',
            'is_featured' => $this->input->post('is_featured') ? 1 : 0,
            'created_by' => $this->session->userdata('admin_id'),
        ];

        $car_id = $this->Car_model->create($data);

        $this->upload_images($car_id);

        echo json_encode([
            'status' => true,
            'message' => 'Car added successfully'
        ]);
    }

    // ================= EDIT VIEW =================
    public function edit($id) {

        $car = $this->Car_model->get($id);
        if (!$car) show_404();

        $this->data['car'] = $car;
        $this->data['car_images'] = $this->Car_image_model->get_by_car($id);
        $this->data['brands'] = $this->Brand_model->get_all(FALSE);
        $this->data['models'] = $this->Car_model_model->get_by_brand($car->brand_id);
        $this->data['cities'] = $this->City_model->get_all(FALSE);
        $this->data['fuel_types'] = $this->Fuel_type_model->get_all();
        $this->data['transmissions'] = $this->Transmission_model->get_all();

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/cars/edit', $this->data);
        $this->load->view('admin/layout/footer');
    }

    // ================= UPDATE (AJAX) =================
    public function update($id) {

        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('brand_id', 'Brand', 'required');
        $this->form_validation->set_rules('model_id', 'Model', 'required');

        if (!$this->form_validation->run()) {
            echo json_encode([
                'status' => false,
                'errors' => validation_errors()
            ]);
            return;
        }

        $data = [
            'brand_id' => $this->input->post('brand_id'),
            'model_id' => $this->input->post('model_id'),
            'variant' => $this->input->post('variant', TRUE),
            'year' => $this->input->post('year'),
            'price' => $this->input->post('price'),
            'city_id' => $this->input->post('city_id'),
        ];

        $this->Car_model->update($id, $data);

        $this->upload_images($id);

        echo json_encode([
            'status' => true,
            'message' => 'Car updated successfully'
        ]);
    }

    // ================= DELETE =================
    public function delete($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->Car_model->delete($id);

        echo json_encode(['status' => true]);
    }
    
    // ================= MODELS BY BRAND =================
    public function models_by_brand() {
        $brand_id = $this->input->get('brand_id');
        echo json_encode($this->Car_model_model->get_by_brand($brand_id));
    }

    // ================= DELETE IMAGE =================
    public function delete_image($image_id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $image = $this->Car_image_model->get($image_id);
        if (!$image) {
            echo json_encode(['status' => false, 'message' => 'Image not found']);
            return;
        }

        // Delete physical file
        $filepath = FCPATH . $image->image_path;
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        // Delete database record
        $this->Car_image_model->delete($image_id);

        echo json_encode(['status' => true]);
    }

    // ================= IMAGE UPLOAD =================
    private function upload_images($car_id) {

        if (empty($_FILES['images']['name'][0])) return;

        $upload_path = FCPATH . 'uploads/cars/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $config = [
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|webp|gif',
            'encrypt_name'  => TRUE,
        ];

        $this->load->library('upload');

        $files = $_FILES['images'];
        $count = count($files['name']);

        $primary = 1;

        for ($i = 0; $i < $count; $i++) {

            if (empty($files['name'][$i])) continue;

            $_FILES['file']['name']     = $files['name'][$i];
            $_FILES['file']['type']     = $files['type'][$i];
            $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
            $_FILES['file']['error']    = $files['error'][$i];
            $_FILES['file']['size']     = $files['size'][$i];

            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {

                $uploadData = $this->upload->data();

                $this->Car_image_model->add([
                    'car_id' => $car_id,
                    'image_path' => 'uploads/cars/' . $uploadData['file_name'],
                    'is_primary' => $primary,
                    'display_order' => $i
                ]);

                $primary = 0;

            } else {
                echo json_encode([
                    'status' => false,
                    'error' => $this->upload->display_errors()
                ]);
                return;
            }
        }
    }
}