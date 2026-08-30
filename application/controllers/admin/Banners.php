<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Banners extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('Banner_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        $this->data['banners'] = $this->Banner_model->get_all();
        $this->data['title'] = 'Manage Banners';
        $this->data['entity'] = 'banners';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/list', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function create() {
        $this->data['banner'] = null;
        $this->data['title'] = 'Add Banner';
        $this->data['entity'] = 'banner';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/form', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function store() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('title', 'Title', 'trim|max_length[200]');
        $this->form_validation->set_rules('link_url', 'Link URL', 'trim|max_length[500]');
        $this->form_validation->set_rules('display_order', 'Display Order', 'integer');
        $this->form_validation->set_rules('placement', 'Banner placement', 'required|in_list[home_main,home_sub,new_products,about,contact,custom]');
        $this->form_validation->set_rules('placement_key', 'Custom placement', 'trim|max_length[100]');

        if (!$this->form_validation->run()) {
            $this->json_response(false, validation_errors());
            return;
        }

        if ($this->input->post('placement') === 'custom' && !trim($this->input->post('placement_key', TRUE))) {
            $this->json_response(false, 'Please enter a custom placement name.');
            return;
        }

        if (empty($_FILES['image']['name'])) {
            $this->json_response(false, 'Please select a banner image.');
            return;
        }

        $upload_result = $this->upload_banner_image();
        if (!is_array($upload_result)) {
            $this->json_response(false, $upload_result);
            return;
        }

        $this->Banner_model->create([
            'title' => $this->input->post('title', TRUE),
            'image_path' => $upload_result['path'],
            'link_url' => $this->input->post('link_url', TRUE),
            'placement' => $this->banner_placement(),
            'display_order' => (int) $this->input->post('display_order'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ]);

        $this->json_response(true, 'Banner added successfully');
    }

    public function edit($id) {
        $banner = $this->Banner_model->get($id);
        if (!$banner) {
            show_404();
        }

        $this->data['banner'] = $banner;
        $this->data['title'] = 'Edit Banner';
        $this->data['entity'] = 'banner';

        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/form', $this->data);
        $this->load->view('admin/layout/footer');
    }

    public function update($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $banner = $this->Banner_model->get($id);
        if (!$banner) {
            $this->json_response(false, 'Banner not found.');
            return;
        }

        $this->form_validation->set_rules('title', 'Title', 'trim|max_length[200]');
        $this->form_validation->set_rules('link_url', 'Link URL', 'trim|max_length[500]');
        $this->form_validation->set_rules('display_order', 'Display Order', 'integer');
        $this->form_validation->set_rules('placement', 'Banner placement', 'required|in_list[home_main,home_sub,new_products,about,contact,custom]');
        $this->form_validation->set_rules('placement_key', 'Custom placement', 'trim|max_length[100]');

        if (!$this->form_validation->run()) {
            $this->json_response(false, validation_errors());
            return;
        }

        if ($this->input->post('placement') === 'custom' && !trim($this->input->post('placement_key', TRUE))) {
            $this->json_response(false, 'Please enter a custom placement name.');
            return;
        }

        $data = [
            'title' => $this->input->post('title', TRUE),
            'link_url' => $this->input->post('link_url', TRUE),
            'placement' => $this->banner_placement(),
            'display_order' => (int) $this->input->post('display_order'),
            'is_active' => $this->input->post('is_active') ? 1 : 0,
        ];

        if (!empty($_FILES['image']['name'])) {
            $upload_result = $this->upload_banner_image();
            if (!is_array($upload_result)) {
                $this->json_response(false, $upload_result);
                return;
            }

            $data['image_path'] = $upload_result['path'];
            $this->delete_banner_file($banner->image_path);
        }

        $this->Banner_model->update($id, $data);
        $this->json_response(true, 'Banner updated successfully');
    }

    public function delete($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $banner = $this->Banner_model->get($id);
        if (!$banner) {
            $this->json_response(false, 'Banner not found.');
            return;
        }

        $this->delete_banner_file($banner->image_path);
        $this->Banner_model->delete($id);

        $this->json_response(true, 'Banner deleted successfully');
    }

    private function upload_banner_image() {
        $upload_path = FCPATH . $this->config->item('banners_path');

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config = [
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|webp|gif',
            'encrypt_name' => TRUE,
            'max_size' => 5120,
            'max_width' => 4096,
            'max_height' => 4096,
        ];

        $this->load->library('upload');
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('image')) {
            return $this->upload->display_errors('', '');
        }

        $upload_data = $this->upload->data();

        return [
            'path' => $this->config->item('banners_path') . $upload_data['file_name'],
            'file_name' => $upload_data['file_name'],
        ];
    }

    private function delete_banner_file($image_path) {
        if (!$image_path) {
            return;
        }

        $file_path = FCPATH . $image_path;
        if (is_file($file_path)) {
            unlink($file_path);
        }
    }

    private function banner_placement() {
        $placement = $this->input->post('placement', TRUE);
        if ($placement !== 'custom') {
            return $placement;
        }

        $custom_placement = url_title($this->input->post('placement_key', TRUE), '_', TRUE);
        return $custom_placement ?: 'custom';
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
