<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Home Controller - Landing page
 */
class Home extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Car_model');
        $this->load->model('Brand_model');
        $this->load->model('Banner_model');
        $this->load->model('Product_model');
        $this->load->model('Accessory_category_model');
    }

    public function index() {
        $this->load->model('Car_image_model');
        $this->load->model('Accessory_image_model');
        $this->data['main_banners'] = $this->Banner_model->get_active('home_main');
        $this->data['sub_banners'] = $this->Banner_model->get_active('home_sub');
        $this->data['new_product_banners'] = $this->Banner_model->get_active('new_products');
        $this->data['accessory_categories'] = $this->Accessory_category_model->get_all();
        $this->data['featured_cars'] = $this->Car_model->get_featured(10);
        $this->data['latest_cars'] = $this->Car_model->get_latest(10);
        foreach ($this->data['featured_cars'] as $c) {
            $p = $this->Car_image_model->get_primary($c->id);
            $c->primary_image = $p ? base_url($p->image_path) : base_url('assets/images/placeholder-car.svg');
        }
        foreach ($this->data['latest_cars'] as $c) {
            $p = $this->Car_image_model->get_primary($c->id);
            $c->primary_image = $p ? base_url($p->image_path) : base_url('assets/images/placeholder-car.svg');
        }
        $this->data['popular_brands'] = $this->Brand_model->get_popular(12);
        $this->data['featured_accessories'] = $this->Product_model->get_featured(12);
        $this->data['hot_deals'] = $this->Product_model->get_hot_deals(2);
        $this->data['new_products'] = $this->Product_model->get_new(18);
        $this->data['hot_sold_products'] = $this->Product_model->get_hot_sold(12);
        $this->data['featured_accessories'] = $this->fill_to($this->data['featured_accessories'], 12);
        $this->data['new_products'] = $this->fill_to($this->data['new_products'], 12);
        $this->data['hot_sold_products'] = $this->fill_to($this->data['hot_sold_products'], 12);
        $this->attach_accessory_images($this->data['featured_accessories']);
        $this->attach_accessory_images($this->data['hot_deals']);
        $this->attach_accessory_images($this->data['hot_sold_products']);
        $this->attach_accessory_images($this->data['new_products']);
        $merged = $this->data['featured_cars'];
        $car_ids = array_map(function ($c) { return $c->id; }, $merged);
        foreach ($this->data['latest_cars'] as $c) {
            if (count($merged) >= 10) break;
            if (!in_array($c->id, $car_ids)) {
                $merged[] = $c;
                $car_ids[] = $c->id;
            }
        }
        $this->data['home_cars'] = $merged;
        $this->data['meta_title'] = $this->Setting_model->get('meta_title') ?: 'V Auto Spare - Buy & Sell Cars';
        $this->data['meta_description'] = $this->Setting_model->get('meta_description') ?: 'Find your perfect car. Browse new and used cars.';
        
        $this->load->view('layout/header', $this->data);
        $this->load->view('home/index', $this->data);
        $this->load->view('layout/footer', $this->data);
    }

    /**
     * Top up a homepage product list to $limit items using latest available
     * products, so each slider always has enough cards to scroll.
     */
    private function fill_to($items, $limit) {
        $items = is_array($items) ? $items : [];
        if (count($items) >= $limit) {
            return array_slice($items, 0, $limit);
        }
        $ids = array_map(function ($p) { return $p->id; }, $items);
        $extra = $this->Product_model->get_list(['status' => 'available'], $limit, 0);
        foreach ($extra as $p) {
            if (count($items) >= $limit) break;
            if (!in_array($p->id, $ids)) {
                $items[] = $p;
                $ids[] = $p->id;
            }
        }
        return $items;
    }

    private function attach_accessory_images(&$accessories) {
        foreach ($accessories as $a) {
            $p = $this->Accessory_image_model->get_primary($a->id);
            $a->primary_image = $p ? base_url($p->image_path) : '';
        }
    }
}
