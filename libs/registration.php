<?php
/**
 *
 * Registration functions
 *
 * @filesource https://github.com/NuarHaruha/bay-rp/blob/master/libs/registration.php
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-rp/blob/master/libs/install.php
 * @since   1.0
 */

/**
 * get total users count
 * @uses WP_User_Query
 *
 * @return int      numbers of users
 */
function mc_rp_get_all_users_count(){
    $users = new WP_User_Query(array('count_total'=>true));
    return (int) $users->get_total();
}

/**
 * get total stockist count
 * @uses get_users() WP_User_Query object
 *
 * @param bool $count           set return count number only, true by default
 * @return mixed|array|int      numbers of stockist if $count true, array of user WP_User_Query object on false
 */
function mc_rp_get_all_stockist_count($count = true){

    $users = get_users(array('role'=> SKTYPE::ST_ROLE));

    if (empty($users)) return 0;

    if ($count){
        return count($users);
    } else {
        return $users;
    }
}

/**
 * Get count of total user registered for current month
 * @uses $wpdb::get_var()
 * @uses RPTYPE::CL_CURMONTH()
 *
 * @return int      numbers of users
 */
function mc_rp_get_total_user_curmonth(){
    global $wpdb;

    $db     = $wpdb->users;
    $sql    = "SELECT COUNT(*) FROM $db WHERE ". RPTYPE::CL_CURMONTH('user_registered');
    return (int) $wpdb->get_var($sql);
}

/**
 * Get count of total stockist registered for current month
 *
 * @return int      numbers of users
 */
function mc_rp_get_total_stockist_curmonth(){

    $users = mc_rp_get_all_stockist_count(false);

    $count = 0;

    if (!empty($users)){
        foreach($users as $user){
            $curmonth   = date("m-y");
            $date   = date("m-y", strtotime($user->data->user_registered));
            if ($date == $curmonth){
                $count++;
            }
        }
    }

    unset($users);
    return $count;
}