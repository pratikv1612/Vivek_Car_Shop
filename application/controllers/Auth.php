<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller - User login, register, account
 */
class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Saved_car_model');
        $this->load->model('Inquiry_model');
        $this->load->model('Shopping_model');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
    }

    public function login() {

        if ($this->session->userdata('user_id')) {
            redirect('account');
        }

        $data = [];

        // Validation
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == TRUE) {

            $email = $this->input->post('email', TRUE);
            $password = $this->input->post('password');

            $user = $this->User_model->get_by_email($email);

            // IMPORTANT: removed is_blocked temporarily
            if ($user && password_verify($password, $user->password)) {

                $this->session->set_userdata([
                    'user_id'   => $user->id,
                    'user_name' => $user->name,
                    'user_email'=> $user->email,
                ]);
                $this->Shopping_model->merge_guest($user->id, $this->session->userdata('shop_lists') ?: []);
                $this->session->unset_userdata('shop_lists');

                redirect('account');

            } else {
                $data['error'] = 'Invalid email or password';
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('auth/login', $data);
        $this->load->view('layout/footer');
    }

    public function register() {

        if ($this->session->userdata('user_id')) {
            redirect('account');
        }

        $data = [];

        $this->form_validation->set_rules('name', 'Name', 'required|min_length[2]');
        $this->form_validation->set_rules('email','Email','required|valid_email|is_unique[users.email]',
            [
                'is_unique' => 'This email is already registered'
            ]
        );

        $this->form_validation->set_rules('phone','Phone','required|regex_match[/^[0-9]{10}$/]',
            [
                'required' => 'Phone number is required',
                'regex_match' => 'Enter valid 10 digit phone number'
            ]
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() == TRUE) {

            $insert = [
                'name' => $this->input->post('name', TRUE),
                'email' => $this->input->post('email', TRUE),
                'phone' => $this->input->post('phone', TRUE),
                'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            ];

            $user_id = $this->User_model->create($insert);
            $user = $this->User_model->get($user_id);

            $this->session->set_userdata([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]);
            $this->Shopping_model->merge_guest($user->id, $this->session->userdata('shop_lists') ?: []);
            $this->session->unset_userdata('shop_lists');

            redirect('account');
        }

        $this->load->view('layout/header', $data);
        $this->load->view('auth/register', $data);
        $this->load->view('layout/footer');
    }

    public function logout() {
        $this->session->unset_userdata(array('user_id', 'user_name', 'user_email'));
        redirect('login');
    }       

    public function account() {

        if (!$this->session->userdata('user_id')) {
            redirect('login');
        }

        $user_id = $this->session->userdata('user_id');

        $user = $this->User_model->get($user_id);
        if (!$user) {
            $this->session->sess_destroy();
            redirect('login');
        }

        $this->data['user'] = $user;
        $this->data['saved_cars'] = $this->Saved_car_model->get_by_user($user_id);
        $this->data['inquiries'] = $this->Inquiry_model->get_by_user($user_id);
        $this->data['billing_history'] = [];
        $this->data['meta_title'] = 'My Account - ' . $this->data['site_name'];

        $this->load->view('layout/header', $this->data);
        $this->load->view('auth/account', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    public function toggle_save() {

        if (!$this->session->userdata('user_id')) {
            echo json_encode([
                'success' => false,
                'message' => 'Please login first'
            ]);
            return;
        }

        $car_id = (int) $this->input->post('car_id');
        if (!$car_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid car ID'
            ]);
            return;
        }

        $user_id = $this->session->userdata('user_id');

        if ($this->Saved_car_model->is_saved($user_id, $car_id)) {

            $this->Saved_car_model->remove($user_id, $car_id);

            echo json_encode([
                'success' => true,
                'saved' => false
            ]);

        } else {

            $this->Saved_car_model->add($user_id, $car_id);

            echo json_encode([
                'success' => true,
                'saved' => true
            ]);
        }
    }
}
