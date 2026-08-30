<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accessories extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('Accessory_model');
        $this->load->model('Accessory_image_model');
        $this->load->model('Accessory_category_model');
        $this->load->model('Brand_model');
        $this->load->model('Car_model_model');

        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }

    public function index($page = 1) {
        $per_page = 20;
        $total = $this->db->count_all('accessories');
        $offset = ($page - 1) * $per_page;

        $this->data['accessories'] = $this->db->select('accessories.*, accessory_categories.name as category_name')
            ->from('accessories')
            ->join('accessory_categories', 'accessory_categories.id = accessories.category_id', 'left')
            ->order_by('accessories.id', 'DESC')
            ->limit($per_page, $offset)
            ->get()
            ->result();

        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = ceil($total / $per_page);
        $this->data['title'] = 'Manage Accessories';
        $this->data['success_message'] = $this->session->flashdata('success');
        $this->data['entity'] = 'accessories';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/list', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function create() {
        $this->data['categories'] = $this->Accessory_category_model->get_all(FALSE);
        $this->data['brands'] = $this->Brand_model->get_all(FALSE);
        $this->data['models'] = [];
        $this->data['title'] = 'Add Accessory';
        $this->data['accessory'] = null;
        $this->data['accessory_images'] = [];

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/accessories/create', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function store() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('name', 'Accessory Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');

        if (!$this->form_validation->run()) {
            echo json_encode([
                'status' => false,
                'errors' => validation_errors()
            ]);
            return;
        }

        $data = [
            'name' => $this->input->post('name', TRUE),
            'slug' => url_title($this->input->post('name', TRUE), '-', TRUE),
            'category_id' => $this->input->post('category_id') ?: null,
            'brand_id' => $this->input->post('brand_id') ?: null,
            'model_id' => $this->input->post('model_id') ?: null,
            'price' => $this->input->post('price'),
            'description' => $this->input->post('description', TRUE),
            'compatible_models' => $this->input->post('compatible_models', TRUE),
            'is_available' => $this->input->post('is_available') ? 1 : 0,
        ];

        $accessory_id = $this->Accessory_model->create($data);

        $upload_result = $this->upload_image($accessory_id, false);
        if ($upload_result !== true) {
            echo json_encode([
                'status' => false,
                'errors' => $upload_result
            ]);
            return;
        }

        echo json_encode([
            'status' => true,
            'message' => 'Accessory added successfully'
        ]);
    }

    public function edit($id) {
        $accessory = $this->Accessory_model->get($id);
        if (!$accessory) show_404();

        $this->data['accessory'] = $accessory;
        $this->data['accessory_images'] = $this->Accessory_image_model->get_by_accessory($id);
        $this->data['categories'] = $this->Accessory_category_model->get_all(FALSE);
        $this->data['brands'] = $this->Brand_model->get_all(FALSE);
        $this->data['models'] = $accessory->brand_id ? $this->Car_model_model->get_by_brand($accessory->brand_id) : [];
        $this->data['title'] = 'Edit Accessory';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/accessories/edit', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function update($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('name', 'Accessory Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');

        if (!$this->form_validation->run()) {
            echo json_encode([
                'status' => false,
                'errors' => validation_errors()
            ]);
            return;
        }

        $data = [
            'name' => $this->input->post('name', TRUE),
            'slug' => url_title($this->input->post('name', TRUE), '-', TRUE),
            'category_id' => $this->input->post('category_id') ?: null,
            'brand_id' => $this->input->post('brand_id') ?: null,
            'model_id' => $this->input->post('model_id') ?: null,
            'price' => $this->input->post('price'),
            'description' => $this->input->post('description', TRUE),
            'compatible_models' => $this->input->post('compatible_models', TRUE),
            'is_available' => $this->input->post('is_available') ? 1 : 0,
        ];

        $this->Accessory_model->update($id, $data);

        $replace_images = !empty($_FILES['images']['name'][0]);
        $upload_result = $this->upload_image($id, $replace_images);
        if ($upload_result !== true) {
            echo json_encode([
                'status' => false,
                'errors' => $upload_result
            ]);
            return;
        }

        echo json_encode([
            'status' => true,
            'message' => 'Accessory updated successfully'
        ]);
    }

    public function delete($id) {
        if ($this->input->is_ajax_request()) {
            $accessory_images = $this->Accessory_image_model->get_by_accessory($id);
            foreach ($accessory_images as $image) {
                $path = FCPATH . $image->image_path;
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $this->Accessory_model->delete($id);
            echo json_encode(['status' => true]);
            return;
        }

        $this->Accessory_model->delete($id);
        $this->session->set_flashdata('success', 'Accessory deleted.');
        redirect('admin/accessories');
    }

    // ================= DELETE IMAGE =================
    public function delete_image($image_id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $image = $this->Accessory_image_model->get($image_id);
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
        $this->Accessory_image_model->delete($image_id);

        echo json_encode(['status' => true]);
    }

    private function upload_image($accessory_id, $replace_existing = false) {
        if (empty($_FILES['images']['name'][0])) {
            return true;
        }

        $upload_path = FCPATH . 'uploads/accessories/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if ($replace_existing) {
            $existing_images = $this->Accessory_image_model->get_by_accessory($accessory_id);
            foreach ($existing_images as $image) {
                $image_file = FCPATH . $image->image_path;
                if (file_exists($image_file)) {
                    unlink($image_file);
                }
            }
            $this->Accessory_image_model->delete_by_accessory($accessory_id);
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|webp|gif',
            'encrypt_name' => TRUE,
            'max_size' => 5120,
        ];

        $this->load->library('upload');
        $files = $_FILES['images'];
        $count = count($files['name']);
        $primary = 1;
        $errors = [];

        for ($i = 0; $i < $count; $i++) {
            if (empty($files['name'][$i])) continue;

            $_FILES['file'] = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i],
            ];

            $this->upload->initialize($config);
            if ($this->upload->do_upload('file')) {
                $uploadData = $this->upload->data();
                $this->Accessory_image_model->add([
                    'accessory_id' => $accessory_id,
                    'image_path' => 'uploads/accessories/' . $uploadData['file_name'],
                    'is_primary' => $primary,
                    'display_order' => $i,
                ]);
                $primary = 0;
            } else {
                $errors[] = $this->upload->display_errors('', '');
            }
        }

        return empty($errors) ? true : implode(' ', $errors);
    }
}
