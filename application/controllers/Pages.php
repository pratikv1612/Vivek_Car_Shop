<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends MY_Controller {

    public function about() {
        $this->data['meta_title'] = 'About Us - ' . $this->data['site_name'];
        $this->load->view('layout/header', $this->data);
        $this->load->view('pages/about', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function contact() {
        $this->data['meta_title'] = 'Contact Us - ' . $this->data['site_name'];
        $this->load->view('layout/header', $this->data);
        $this->load->view('pages/contact', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function send_contact() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[150]');
        $this->form_validation->set_rules('message', 'Message', 'trim|required|max_length[2000]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('contact_error', strip_tags(validation_errors()));
            redirect('contact');
        }

        $name = $this->input->post('name', TRUE);
        $email = $this->input->post('email', TRUE);
        $body = $this->input->post('message', TRUE);

        // Save the contact request into the inquiries table (shows in admin panel)
        $this->load->model('Inquiry_model');
        $this->Inquiry_model->create(array(
            'type' => 'contact',
            'customer_name' => $name,
            'customer_email' => $email,
            'message' => $body,
            'user_id' => $this->session->userdata('user_id') ?: NULL,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
        ));

        $this->session->set_flashdata('contact_success', 'Thanks for contacting us. We will get back to you shortly.');
        redirect('contact');
    }
}
