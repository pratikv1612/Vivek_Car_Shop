<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller - Loads common data for frontend
 */
class MY_Controller extends CI_Controller {

    protected $data = array();

    public function __construct() {
        parent::__construct();
        $this->load->model('Setting_model');
        $this->load->model('Brand_model');
        $this->load->model('City_model');
        $this->load->model('Accessory_category_model');

        // Global data for views
        $this->data['site_name'] = $this->Setting_model->get('site_name') ?: 'V Auto Spare';
        $this->data['meta_keywords'] = $this->Setting_model->get('meta_keywords') ?: 'cars, buy car, sell car';
        $this->data['whatsapp_number'] = $this->Setting_model->get_whatsapp_number();
        $this->data['whatsapp_url'] = $this->Setting_model->get('whatsapp_number') 
            ? $this->Setting_model->get_whatsapp_url('Hello! I have an inquiry.') 
            : '#';
        $this->data['brands'] = $this->Brand_model->get_all();
        $this->data['cities'] = $this->City_model->get_all();
        $this->data['accessory_categories'] = $this->Accessory_category_model->get_all();
    }
}
