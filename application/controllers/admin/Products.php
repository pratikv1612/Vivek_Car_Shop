<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('Product_model');
        $this->load->model('Accessory_image_model');
        $this->load->model('Accessory_category_model');
        $this->load->model('Brand_model');
        $this->load->model('Car_model_model');

        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }

    public function index($page = 1) {
        $per_page = 20;
        $filters = [
            'status' => $this->input->get('status'),
            'is_featured' => $this->input->get('featured'),
            'is_new' => $this->input->get('new'),
            'is_hot_sold' => $this->input->get('hot_sold'),
        ];

        $total = $this->Product_model->count_list($filters);
        $offset = ($page - 1) * $per_page;

        $this->data['products'] = $this->Product_model->get_list($filters, $per_page, $offset);
        $this->data['filters'] = $filters;
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = ceil($total / $per_page);
        $this->data['title'] = 'Manage Products';
        $this->data['entity'] = 'products';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/list', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function create() {
        $this->set_form_data(null);
        $this->data['title'] = 'Add Product';
        $this->data['entity'] = 'product';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/form', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function store() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        if (!$this->validate_product()) {
            $this->json_response(false, validation_errors());
            return;
        }

        $product_id = $this->Product_model->create($this->product_payload());

        $upload_result = $this->upload_images($product_id, false);
        if ($upload_result !== true) {
            $this->json_response(false, $upload_result);
            return;
        }

        $this->json_response(true, 'Product added successfully');
    }

    public function edit($id) {
        $product = $this->Product_model->get($id);
        if (!$product) {
            show_404();
        }

        $this->set_form_data($product);
        $this->data['title'] = 'Edit Product';
        $this->data['entity'] = 'product';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/form', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function update($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $product = $this->Product_model->get($id);
        if (!$product) {
            $this->json_response(false, 'Product not found');
            return;
        }

        if (!$this->validate_product()) {
            $this->json_response(false, validation_errors());
            return;
        }

        $this->Product_model->update($id, $this->product_payload());

        $replace_images = !empty($_FILES['images']['name'][0]);
        $upload_result = $this->upload_images($id, $replace_images);
        if ($upload_result !== true) {
            $this->json_response(false, $upload_result);
            return;
        }

        $this->json_response(true, 'Product updated successfully');
    }

    public function delete($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $images = $this->Accessory_image_model->get_by_accessory($id);
        foreach ($images as $image) {
            $path = FCPATH . $image->image_path;
            if (is_file($path)) {
                unlink($path);
            }
        }

        $this->Product_model->delete($id);
        $this->json_response(true, 'Product deleted successfully');
    }

    public function delete_image($image_id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $image = $this->Accessory_image_model->get($image_id);
        if (!$image) {
            $this->json_response(false, 'Image not found');
            return;
        }

        $path = FCPATH . $image->image_path;
        if (is_file($path)) {
            unlink($path);
        }

        $this->Accessory_image_model->delete($image_id);
        $this->json_response(true, 'Image deleted successfully');
    }

    private function set_form_data($product) {
        $this->data['product'] = $product;
        $this->data['product_images'] = $product ? $this->Accessory_image_model->get_by_accessory($product->id) : [];
        $this->data['categories'] = $this->Accessory_category_model->get_all(FALSE);
        $this->data['brands'] = $this->Brand_model->get_all(FALSE);
        $this->data['models'] = ($product && $product->brand_id) ? $this->Car_model_model->get_by_brand($product->brand_id) : [];
    }

    private function validate_product() {
        $this->form_validation->set_rules('name', 'Product Name', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');
        $this->form_validation->set_rules('product_status', 'Product Status', 'required|in_list[available,sold,hidden]');
        return $this->form_validation->run();
    }

    private function product_payload() {
        $status = $this->input->post('product_status') ?: 'available';

        return [
            'name' => $this->input->post('name', TRUE),
            'slug' => url_title($this->input->post('name', TRUE), '-', TRUE) . '-' . substr(md5(uniqid('', true)), 0, 6),
            'category_id' => $this->input->post('category_id') ?: null,
            'brand_id' => $this->input->post('brand_id') ?: null,
            'model_id' => $this->input->post('model_id') ?: null,
            'price' => $this->input->post('price'),
            'description' => $this->input->post('description', TRUE),
            'compatible_models' => $this->input->post('compatible_models', TRUE),
            'is_available' => $status === 'hidden' ? 0 : 1,
            'is_featured' => $this->input->post('is_featured') ? 1 : 0,
            'is_new' => $this->input->post('is_new') ? 1 : 0,
            'is_hot_sold' => $this->input->post('is_hot_sold') ? 1 : 0,
            'product_status' => $status,
            'display_order' => (int) $this->input->post('display_order'),
        ];
    }

    private function upload_images($product_id, $replace_existing = false) {
        if (empty($_FILES['images']['name'][0])) {
            return true;
        }

        $upload_path = FCPATH . 'uploads/accessories/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        if ($replace_existing) {
            $existing_images = $this->Accessory_image_model->get_by_accessory($product_id);
            foreach ($existing_images as $image) {
                $image_file = FCPATH . $image->image_path;
                if (is_file($image_file)) {
                    unlink($image_file);
                }
            }
            $this->Accessory_image_model->delete_by_accessory($product_id);
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
                $upload_data = $this->upload->data();
                $this->Accessory_image_model->add([
                    'accessory_id' => $product_id,
                    'image_path' => 'uploads/accessories/' . $upload_data['file_name'],
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

    private function json_response($status, $message) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => (bool) $status,
                'message' => $message,
                'errors' => $status ? '' : $message,
            ]));
    }
}
