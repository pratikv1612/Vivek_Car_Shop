<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Inquiries Management
 */
class Inquiries extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Inquiry_model');
    }

    public function index($page = 1) {
        $per_page = 30;
        $filters = array(
            'type' => $this->input->get('type'),
            'from_date' => $this->input->get('from_date'),
            'to_date' => $this->input->get('to_date'),
        );
        $total = $this->Inquiry_model->count_list($filters);
        $offset = ($page - 1) * $per_page;
        
        $this->data['inquiries'] = $this->Inquiry_model->get_list($filters, $per_page, $offset);
        $this->data['filters'] = $filters;
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = ceil($total / $per_page);
        $this->data['title'] = 'Inquiries';
        $this->data['entity'] = 'inquiries';
        
        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/list', $this->data);
        $this->load->view('admin/layout/footer', $this->data);
    }
}
