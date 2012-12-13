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

/**
 * Monthly registration chart
 * type: line chart
 */
function mc_rp_user_month_charts(){

    $chart = new Highchart();

    $chart->chart                   = array(
                                        'renderTo'  => 'container-m',
                                        'type'      => 'line');

    $chart->title                   = array(
                                        'text'      => 'Monthly Registration Statistics');

    $chart->subtitle->text          = 'Isra\'life Members for '.date("Y");;
    $chart->xAxis->categories       = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    $chart->yAxis->title->text      = 'Members Registration';
    $chart->tooltip->enabled        = true;
    $chart->tooltip->crosshairs     = true;
    $chart->tooltip->formatter      = new HighchartJsExpr("function() {
                return '<b>'+ this.series.name +'</b><br/>'+
                    this.x +': '+ this.y;
            }");
    $chart->plotOptions->line->dataLabels->enabled = true;
    $chart->plotOptions->line->enableMouseTracking = true;
    $chart->credits->enabled = false;

    foreach(array('Members','Inactive','Stockist') as $series){
        $chart->series[] = array('name' => $series,'data' => mc_rp_user_type($series));
    }
?>
<div id="container-m"></div>
<script type="text/javascript"><?php echo $chart->render("chart1"); ?></script>
<?php
}

/**
 *  get json type data series for Highchart
 */
function mc_rp_user_type($type){

    $type = strtolower($type);
    $meta = 'status_option_active';

    switch ($type){
        case 'active':      $meta = 'status_option_active'; break;
        case 'inactive':    $meta = 'status_option_inactive'; break;
        case 'stockist':    $meta = 'user_type_option_stokis'; break;
    }

    $months = mc_get_months_highchart();
    $users  = ($type == 'members') ? get_users() : $users = get_users(array('meta_key'=> $meta));

    if (has_count($users)){
        foreach($users as $index => $user){
            $date       = $user->user_registered;
            $timestamp  = strtotime($date);
            // is current year
            if (date('Y', $timestamp) == date('Y')){
                $user_month = date("M", $timestamp);
                $months[$user_month]++;
            }
        }
    }

    $months = join(",",$months);
    return new HighchartJsExpr("[".$months."]");
}