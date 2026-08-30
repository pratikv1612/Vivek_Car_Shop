<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Users Management
 */
class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index($page = 1) {
        $per_page = 30;
        $filters = array(
            'is_blocked' => $this->input->get('status') === 'blocked' ? 1 : ($this->input->get('status') === 'active' ? 0 : NULL),
            'keyword' => $this->input->get('keyword'),
        );
        $total = $this->User_model->count_all($filters);
        $offset = ($page - 1) * $per_page;
        
        $this->data['users'] = $this->User_model->get_all($filters, $per_page, $offset);
        $this->data['filters'] = $filters;
        $this->data['total'] = $total;
        $this->data['page'] = $page;
        $this->data['total_pages'] = ceil($total / $per_page);
        $this->data['title'] = 'Manage Users';
        $this->data['entity'] = 'users';
        
        $this->load->view('admin/layout/header', $this->data);
        $this->load->view('admin/list', $this->data);
        $this->load->view('admin/layout/footer', $this->data);
    }

    public function toggle_block($id) {
        $user = $this->User_model->get($id);
        if (!$user) show_404();
        
        $this->User_model->update($id, array('is_blocked' => $user->is_blocked ? 0 : 1));
        $this->session->set_flashdata('success', $user->is_blocked ? 'User unblocked.' : 'User blocked.');
        redirect('admin/users');
    }
}
