<?php
function get_all_sales(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT COUNT(*) total FROM $db";

    return (int) $wpdb->get_var($sql);
}

function get_all_approved_sales(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT COUNT(*) total FROM $db WHERE order_status='approved'";

    return (int) $wpdb->get_var($sql);
}

function get_all_pending_sales(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT COUNT(*) total FROM $db WHERE order_status='pending'";

    return (int) $wpdb->get_var($sql);
}

function sum_all_approved_sales_month(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT SUM(total_amount) FROM $db WHERE order_status='approved' AND ".RPTYPE::CL_CURMONTH('created_date');

    return (int) $wpdb->get_var($sql);
}

function sum_all_approved_orders_month(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT count(*) total FROM $db WHERE order_status='approved' AND ".RPTYPE::CL_CURMONTH('created_date');

    return (int) $wpdb->get_var($sql);
}

function sum_all_avg_orders_month(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT AVG(total_amount) total FROM $db WHERE ".RPTYPE::CL_CURMONTH('created_date');;

    return (int) $wpdb->get_var($sql);
}

function sum_all_approved_sales(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT SUM(total_amount) total FROM $db WHERE order_status='approved'";

    return (int) $wpdb->get_var($sql);
}

function sum_all_approved_orders(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT count(*) total FROM $db WHERE order_status='approved'";

    return (int) $wpdb->get_var($sql);
}

function sum_all_avg_orders(){
    global $wpdb;

    $db = $wpdb->base_prefix.'mc_invoices';

    $sql = "SELECT AVG(total_amount) total FROM $db GROUP BY created_date";

    return (int) $wpdb->get_var($sql);
}