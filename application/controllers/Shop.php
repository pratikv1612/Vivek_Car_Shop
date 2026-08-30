<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shop extends MY_Controller {
    public function __construct() { parent::__construct(); $this->load->model(['Shopping_model', 'Accessory_model', 'Accessory_image_model']); $this->load->library('session'); }

    public function add($type) { $this->mutate($type, 'add'); }
    public function remove($type) { $this->mutate($type, 'remove'); }
    public function quantity() { $this->mutate('cart', 'quantity'); }
    private function mutate($type, $action) {
        if (!$this->input->is_ajax_request() || !in_array($type, ['cart','wishlist','compare'], true)) show_404();
        $id = (int)$this->input->post('product_id'); $product = $this->Accessory_model->get($id);
        if (!$product) { $this->json(false, 'Product not found.'); return; }
        $user_id = (int)$this->session->userdata('user_id');
        if ($user_id) {
            if ($action === 'add') $this->Shopping_model->add($user_id, $type, $id);
            if ($action === 'remove') $this->Shopping_model->remove($user_id, $type, $id);
            if ($action === 'quantity') $this->Shopping_model->set_quantity($user_id, $id, (int)$this->input->post('quantity'));
        } else {
            $lists = $this->session->userdata('shop_lists') ?: ['cart'=>[], 'wishlist'=>[], 'compare'=>[]];
            if ($action === 'add') $lists[$type][$id] = $type === 'cart' ? (($lists[$type][$id] ?? 0) + 1) : 1;
            if ($action === 'remove') unset($lists[$type][$id]);
            if ($action === 'quantity') $lists['cart'][$id] = max(1, (int)$this->input->post('quantity'));
            $this->session->set_userdata('shop_lists', $lists);
        }
        $this->json(true, ucfirst($type) . ' updated.', ['counts'=>$this->counts()]);
    }
    public function cart() { $this->page('cart'); }
    public function wishlist() { $this->page('wishlist'); }
    public function compare() { $this->page('compare'); }
    private function page($type) { $this->data['shop_items'] = $this->products($type); $this->data['shop_counts'] = $this->counts(); $this->data['page_type'] = $type; $this->data['meta_title'] = ucfirst($type).' - '.$this->data['site_name']; $this->load->view('layout/header',$this->data); $this->load->view('shop/'.$type,$this->data); $this->load->view('layout/footer',$this->data); }
    public function checkout() { $this->data['shop_items'] = $this->products('cart'); $this->data['shop_counts'] = $this->counts(); $this->data['meta_title'] = 'Checkout - '.$this->data['site_name']; $this->load->view('layout/header',$this->data); $this->load->view('shop/checkout',$this->data); $this->load->view('layout/footer',$this->data); }
    public function summary() { if (!$this->input->is_ajax_request()) show_404(); $items = $this->products('cart'); $this->json(true, '', ['counts'=>$this->counts(), 'items'=>$items, 'total'=>$this->total($items)]); }
    public function counts() { $result = ['cart'=>0,'wishlist'=>0,'compare'=>0]; $user_id = (int)$this->session->userdata('user_id'); if ($user_id) { foreach ($result as $type=>$_) foreach ($this->Shopping_model->items($user_id,$type) as $item) $result[$type] += $type === 'cart' ? $item->quantity : 1; } else { $lists=$this->session->userdata('shop_lists') ?: []; foreach ($result as $type=>$_) $result[$type] = $type === 'cart' ? array_sum($lists[$type] ?? []) : count($lists[$type] ?? []); } return $result; }
    private function products($type) { $user_id=(int)$this->session->userdata('user_id'); $raw=$user_id ? $this->Shopping_model->items($user_id,$type) : []; if (!$user_id) foreach (($this->session->userdata('shop_lists')[$type] ?? []) as $id=>$quantity) $raw[]=(object)['accessory_id'=>$id,'quantity'=>$quantity]; $items=[]; foreach ($raw as $row) { $p=$this->Accessory_model->get($row->accessory_id); if (!$p) continue; $image=$this->Accessory_image_model->get_primary($p->id); $p->primary_image=$image ? base_url($image->image_path) : base_url('assets/images/placeholder-product.svg'); $p->quantity=$type==='cart' ? (int)$row->quantity : 1; $items[]=$p; } return $items; }
    private function total($items) { $total=0; foreach ($items as $item) $total += $item->price*$item->quantity; return $total; }
    private function json($status,$message,$data=[]) { $this->output->set_content_type('application/json')->set_output(json_encode(array_merge(['status'=>$status,'message'=>$message],$data))); }
}
