<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('number_pre_order_qty')) {
    function number_pre_order_qty()
    {
        $CI = &get_instance();
        $CI->load->database();

        $CI->db->where('status_progress', 'Listing');
        return $CI->db->get('analisys_po')->num_rows();
    }
}

if (!function_exists('number_pre_order_pre')) {
    function number_pre_order_pre()
    {
        $CI = &get_instance();
        $CI->load->database();

        $CI->db->where('status_progress', 'Qty');
        return $CI->db->get('analisys_po')->num_rows();
    }
}
