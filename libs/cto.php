<?php
/**
 *
 * CTO functions
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-rp/blob/master/libs/cto.php
 * @since   1.0
 */

function get_sales_turnover_curmonth(){
    $mk_date        = date("m-y");

    /* Cost of sales
     * we don't have this figure yet */
    $turnover = array(
        'cost'      => 0,
        'orders'    => sum_all_approved_orders_month()
    );

    /** Turnover
     * It "should" be calculate base on average orders
     * but for this calculation we just sum all sales
     * for current month
     * */
    $turnover['amount'] = sum_all_approved_sales_month();
    $turnover['gross']  =  (float) ($turnover['amount'] - $turnover['cost']) ;

    update_option('turnover-'.$mk_date, $turnover['gross']);

    return $turnover;
}

function get_expenses_curmonth(){

    /* meta key suffix */
    $mk_date        = date("m-y");

    $expenses = array();

    $registration = array(
        'amount' => get_total_registration_bonus_sum_curmonth(),
        'count'  => get_total_registration_bonus_count_curmonth()
    );

    $expenses['registration'] = $registration;

    $rates = get_option(SKTYPE::MK_SALES_BONUS, false);

    $stockist = array(
        'pv' => array(
            'state'       => get_stockist_pv_sum('state'),
            'district'    => get_stockist_pv_sum('district'),
            'mobile'      => get_stockist_pv_sum('mobile')
        ),
        'count' => array(
            'state'       => get_stockist_count('state'),
            'district'    => get_stockist_count('district'),
            'mobile'      => get_stockist_count('mobile')
        ),
        'rates' => array(
            'type'        => $rates['type'],
            'state'       => (float) $rates['state'],
            'district'    => (float) $rates['district'],
            'mobile'      => (float) $rates['mobile']
        )
    );

    switch($stockist['rates']['type']){
        case 'PERCENT':
            $stockist_sum_pv =  array(
                'state'       => (empty($stockist['pv']['state'])) ?
                    0 : ( ($stockist['pv']['state'] * $stockist['rates']['state'])/100 ),
                'district'    => (empty($stockist['pv']['district'])) ?
                    0 : ( ($stockist['pv']['district'] * $stockist['rates']['district'])/100 ),
                'mobile'      => (empty($stockist['pv']['mobile'])) ?
                    0 : ( ($stockist['pv']['mobile'] * $stockist['rates']['mobile'])/100 )
            );
            break;
        case 'RM':
        case 'PV':
        default:
        $stockist_sum_pv =  array(
            'state'       => (empty($stockist['pv']['state'])) ?
                0 : ($stockist['pv']['state'] * $stockist['rates']['state']),
            'district'    => (empty($stockist['pv']['district'])) ?
                0 : ($stockist['pv']['district'] * $stockist['rates']['district']),
            'mobile'      => (empty($stockist['pv']['mobile'])) ?
                0 : ($stockist['pv']['mobile'] * $stockist['rates']['mobile'])
        );
        break;
    }

    $stockist['sum_pv'] =  $stockist_sum_pv;

    $stockist_pv_sum = array_sum(array(
        $stockist['sum_pv']['state'],
        $stockist['sum_pv']['district'],
        $stockist['sum_pv']['mobile']
    ));

    $expenses['stockist'] = $stockist;

    $expenses['group_pv'] = array(
        'min'   => array(
            'amount'    => get_ttl_user_group_pv_min_amount(),
            'count'     => (int) get_ttl_user_group_pv_min_count()
        ),
        'max'   => array(
            'amount'    => get_ttl_user_group_pv_max_amount(),
            'count'     => (int) get_ttl_user_group_pv_max_count()
        )
    );

    /* sums */
    $group_pv_sum = array_sum(array(
            $expenses['group_pv']['max']['amount'],
            $expenses['group_pv']['min']['amount']
        )
    );

    $total_expense = array_sum(
        array(
            $expenses['registration'],
            $stockist_pv_sum,
            $group_pv_sum)
    );

    $expenses['total'] = (int) $total_expense;

    /* save meta data */
    update_option('expenses-'.$mk_date, $expenses['total']);

    unset($registration, $stockist, $group_pv, $user_pv_sum, $total_expense);

    return _obj($expenses);
}

function get_total_registration_bonus_count_curmonth(){
    global $wpdb;

    $db = BTYPE::DB(BTYPE::DB_PRIMARY);
    $type = $type   = BTYPE::BONUS_TYPE_RM;;

    $sql = "SELECT COUNT(*) FROM $db WHERE bonus_type='$type' AND ". RPTYPE::CL_CURMONTH('date');

    return (int) $wpdb->get_var($sql);
}

function get_total_registration_bonus_sum_curmonth(){
    global $wpdb;

    $db = BTYPE::DB(BTYPE::DB_PRIMARY);
    $type = $type   = BTYPE::BONUS_TYPE_RM;;

    $sql = "SELECT SUM(bonus_value) FROM $db WHERE bonus_type='$type' AND ". RPTYPE::CL_CURMONTH('date');

    return (int) $wpdb->get_var($sql);
}

function get_stockist($type){
    global $wpdb;

    $db = SKTYPE::DB(SKTYPE::DB_PRIMARY);

    $sql = "SELECT * FROM $db WHERE type=%s";

    $results = $wpdb->get_results($wpdb->prepare($sql,$type) );

    return $results;

}

function get_stockist_count($type){
    global $wpdb;

    $db = SKTYPE::DB(SKTYPE::DB_PRIMARY);
    $sql = "SELECT COUNT(*) FROM $db WHERE type='$type'";

    $results = $wpdb->get_var($sql);

    return ($results) ? (int) $results : 0;
}

function get_stockist_pv_sum($type='state'){
    global $wpdb;

    $stockist = get_stockist($type);

    $result = 0;

    if ($stockist) {
         foreach($stockist as $i => $s){
             $result += get_user_pv_sum_curmonth($s->stockist_uid);
         }
    }

    return  $result;
}

function get_user_pv_sum_curmonth($uid){
    global $wpdb;

    $db = BTYPE::DB(BTYPE::DB_PRIMARY);
    $uid = (int) $uid;
    $type = BTYPE::BONUS_TYPE_PV;

    $sql = "SELECT SUM(bonus_value) FROM $db WHERE bonus_uid=$uid AND bonus_type='$type' AND ".RPTYPE::CL_CURMONTH('date');

    $result = $wpdb->get_var($sql);

    return (($result) ? (int) $result : 0);
}

function get_user_group_pv_min(){
    $args = array(
        'meta_query' => array(
            array(
                'key' => 'mc_pv',
                'value' => array( 35, 74 ),
                'type' => 'numeric',
                'compare' => 'BETWEEN'
            )
        )
    );

    $users = new WP_User_Query($args);

    return ($users) ? $users : 0 ;
}

function get_user_group_pv_max(){

    $args = array(
        'meta_query' => array(
            array(
                'key' => 'mc_pv',
                'value' => 75,
                'type' => 'numeric',
                'compare' => '>='
            )
        )
    );

    $users = new WP_User_Query($args);
    return ($users) ? $users : 0 ;
}

function get_ttl_user_group_pv_min_count(){
    $users = get_user_group_pv_min();

    return ($users) ? $users->total_users : 0;
}

function get_ttl_user_group_pv_max_count(){
    $users = get_user_group_pv_max();

    return($users) ? $users->total_users : 0;
}

function get_ttl_user_group_pv_max_amount(){

    $users = get_user_group_pv_max();
    $results = 0;

    if ( !empty( $users->results ) ) {

        foreach ( $users->results as $user ) {
            $uid = (int) $user->data->ID;
            $pv = array_sum(mc_get_all_downlines_pv($uid));
            $results += $pv;
        }

    }
    return ($results) ? (int) $results : 0;
}

function get_ttl_user_group_pv_min_amount(){

    $users = get_user_group_pv_min();
    $results = 0;

    if ( !empty( $users->results ) ) {
        foreach ( $users->results as $user ) {
            $uid = (int) $user->data->ID;
            $pv = array_sum(mc_get_all_downlines_pv($uid));
            $results += $pv;
        }
    }
    return ($results) ? (int) $results : 0;
}

