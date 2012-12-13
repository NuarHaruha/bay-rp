<?php
/**
 *
 * Payout functions
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-rp/blob/master/libs/payout.php
 * @since   1.0
 */

function get_sum_monthly_registration_bonus(){
    global $wpdb;

    $db     = BTYPE::DB(BTYPE::DB_PRIMARY);
    $type   = BTYPE::BONUS_TYPE_RM;

    $sql = "SELECT date_format(date,'%m-%y') month_year, sum(bonus_value) amount FROM $db WHERE bonus_type='$type' GROUP BY YEAR(date), MONTH(date) ORDER BY date";

    return $wpdb->get_results($sql);
}

function get_monthly_registration_bonus(){
    global $wpdb;

    $db     = BTYPE::DB(BTYPE::DB_PRIMARY);
    $type   = BTYPE::BONUS_TYPE_RM;

    $sql = "SELECT date_format(date,'%m-%y') month_year, bonus_value amount FROM $db WHERE bonus_type='$type' ORDER BY YEAR(date), MONTH(date)";

    return $wpdb->get_results($sql);
}

function get_curmonth_registration_bonus(){
    global $wpdb;

    $db     = BTYPE::DB(BTYPE::DB_PRIMARY);
    $type   = BTYPE::BONUS_TYPE_RM;
    $w_month = RPTYPE::CL_CURMONTH('date');

    $sql = "SELECT * FROM $db WHERE bonus_type='$type' AND $w_month ORDER BY YEAR(date), MONTH(date)";

    return $wpdb->get_results($sql);
}

