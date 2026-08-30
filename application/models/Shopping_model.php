<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shopping_model extends CI_Model {
    private $types = ['cart', 'wishlist', 'compare'];

    public function add($user_id, $type, $product_id, $quantity = 1) {
        if (!in_array($type, $this->types, true)) return false;
        $existing = $this->db->where(['user_id' => $user_id, 'list_type' => $type, 'accessory_id' => $product_id])->get('user_product_lists')->row();
        if ($existing) {
            if ($type === 'cart') $this->db->where('id', $existing->id)->update('user_product_lists', ['quantity' => $existing->quantity + $quantity]);
            return true;
        }
        return $this->db->insert('user_product_lists', ['user_id' => $user_id, 'list_type' => $type, 'accessory_id' => $product_id, 'quantity' => $type === 'cart' ? $quantity : 1]);
    }

    public function remove($user_id, $type, $product_id) { return $this->db->where(['user_id' => $user_id, 'list_type' => $type, 'accessory_id' => $product_id])->delete('user_product_lists'); }
    public function set_quantity($user_id, $product_id, $quantity) { return $this->db->where(['user_id' => $user_id, 'list_type' => 'cart', 'accessory_id' => $product_id])->update('user_product_lists', ['quantity' => max(1, (int)$quantity)]); }
    public function items($user_id, $type) { return $this->db->where(['user_id' => $user_id, 'list_type' => $type])->get('user_product_lists')->result(); }
    public function merge_guest($user_id, $guest_lists) { foreach ($this->types as $type) { foreach (($guest_lists[$type] ?? []) as $product_id => $quantity) { $this->add($user_id, $type, (int)$product_id, (int)$quantity); } } }
}
