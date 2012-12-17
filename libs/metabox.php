<?php
/**
 * Metabox widget for mc_report
 *
 * @package     isralife
 * @category    reporting
 *
 * @author      Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @copyright   Copyright (C) 2012, Nuarharuha, MDAG Consultancy
 * @license     http://mdag.mit-license.org/ MIT License
 * @filesource  https://github.com/NuarHaruha/bay-rp/blob/master/libs/metabox.php
 * @version     1.0
 * @access      public
 */

/**
 * report cto metabox
 */

function mb_rp_cto_amount_summary(){
    $expenses   = (float) get_option('expenses-'.date("m-y"), 0);
    $turnover   = (float) get_option('turnover-'.date("m-y"), 0);
    $balance    = (float) get_option('balance-'.date("m-y"),0);
    $cto        = (float) get_option('cto-'.date("m-y"), 0);
    $netprofit  = ($balance - $cto);
?>
<table class="widefat sales-summary">
    <tbody>
    <tr>
        <td class="txt-right" style="width: 40%">Turnover</td>
        <td style="width: 60%">
            <span class="medblue">RM <?php echo mc_currency_filter($turnover);?></span>
        </td>
    </tr>
    <tr>
        <td class="txt-right" style="width: 40%">Expenses</td>
        <td style="width: 60%">
            <span class="medblue" style="color:red">- RM <?php echo mc_currency_filter($expenses);?></span>
        </td>
    </tr>
    <tr>
        <td class="txt-right" style="width: 40%">CTO 12%</td>
        <td style="width: 60%">
            <span class="medblue" style="color:red">- RM <?php echo mc_currency_filter($cto);?></span>
        </td>
    </tr>
    <tr>
        <td class="txt-right" style="width: 40%;border-top:1px solid #ccc">Net Profit</td>
        <td style="width: 60%;border-top:1px solid #ccc">
            <span class="medblue" style="color:green">RM <?php echo mc_currency_filter($netprofit);?></span>
        </td>
    </tr>
    </tbody>
</table>
<?php
}

function mb_rp_expense_cto_summary($placeholder, $options){

    $register_sum = get_total_registration_bonus_sum_curmonth();

    $stockist = array(
        'count' => array(
            'state'       => get_stockist_count('state'),
            'district'    => get_stockist_count('district'),
            'mobile'      => get_stockist_count('mobile')
        )
    );


    $stockist = foreach_push(new stdClass(), $stockist);


    $expenses = (float) get_option('expenses-'.date("m-y"), 0);
    $turnover = (float) get_option('turnover-'.date("m-y"), 0);

    $balance  = ($turnover - $expenses);

    $cto = (($balance * 12) / 100);

    update_option('balance-'.date("m-y"), $balance);
    update_option('cto-'.date("m-y"), $cto);

    $stockist = (($balance * 4) / 100);
    $leader = (($balance * 8) / 100);

    $state = ((1 / 4) * $stockist);
    $district = ((3 / 4) * $stockist);

    $pp = ((3 / 8) * $leader);
    $pe = ((2 / 8) * $leader);
    $pb = ((3 / 8) * $leader);

    ?>
<table class="widefat">
    <thead>
    <tr>
        <th style="width: 44%"></th>
        <th style="width: 20%">Unit/Type</th>
        <th style="width: 20%">Subtotal</th>
        <th style="width: 16%">Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th scope="row"><small>1.</small> Stockist 4%</th>
        <td></td>
        <td></td>
        <td>RM <?php echo mc_currency_filter($stockist);?></td>
    </tr>
    <tr>
        <th scope="row">&mdash; <span class="medblue"><?php echo $stockist->count['state'];?></span> State Stockist</th>
        <td>1%</td>
        <td>RM <?php echo mc_currency_filter($state);?></td>
        <td></td>
    </tr>
    <tr>
        <th scope="row">&mdash; <span class="medblue"><?php echo $stockist->count['district'];?></span> District Stockist</th>

        <td>3%</td>
        <td>RM <?php echo mc_currency_filter($district);?></td>
        <td></td>
    </tr>
    <tr>
        <th scope="row">&mdash; <span class="medblue"><?php echo $stockist->count['mobile'];?></span> Mobile Stockist</th>
        <td>0%</td>
        <td>RM 0.00</td>
        <td></td>
    </tr>
    <tr>
        <th scope="row"><small>2.</small> Leadership Bonus 8%</th>
        <td></td>
        <td></td>
        <td>RM <?php echo mc_currency_filter($leader);?></td>
    </tr>
    <tr>
        <th scope="row">&mdash; Pengurus</th>
        <td>0%</td>
        <td>RM 0.00</td>
        <td></td>
    </tr>
    <tr>
        <th scope="row">&mdash; Pengurus Perak</th>
        <td>3%</td>
        <td>RM <?php echo mc_currency_filter($pp);?></td>
        <td></td>
    </tr>
    <tr>
        <th scope="row">&mdash; Pengurus Emas</th>
        <td>2%</td>
        <td>RM <?php echo mc_currency_filter($pe);?></td>
        <td></td>
    </tr>
    <tr>
        <th scope="row">&mdash; Pengurus Berlian</th>
        <td>3%</td>
        <td>RM <?php echo mc_currency_filter($pb);?></td>
        <td></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2">Total CTO 12%</th>
        <th></th>
        <th>
            RM <?php echo mc_currency_filter($cto);?>
        </th>
    </tr>
    </tfoot>
</table>
<?php
}

/**
 * Current month expenses
 */
function mb_rp_expenses_summary($placeholder, $options){

    /* $placeholder store post data, if within post-type page
     * not used
     */
    unset($placeholder);

    list($turnover, $expenses) = $options['args'];

    /** rates type
     *  @var string
     */
    $rate_symbol = ($expenses->stockist->rates->type == 'PERCENT') ? '%' : $expenses->stockist->rates->type;

    //var_dump($expenses);
?>
<table class="widefat">
    <thead>
    <tr>
        <th style="width: 44%"></th>
        <th style="width: 20%">Unit/Type</th>
        <th style="width: 20%">Subtotal</th>
        <th style="width: 16%">Amount</th>
    </tr>
    </thead>
    <tbody>
    <!-- Begin expenses registration -->
    <tr valign="top">
        <th scope="row">
            <small>1.</small> <strong> Members Registration Bonus</strong>
        </th>
        <td>
            <?php echo $expenses->registration->count;?>
        </td>
        <td>
            RM <?php echo mc_currency_filter($expenses->registration->amount);?>
        </td>
        <td></td>
    </tr>
    <!-- End expenses registration -->
    <!-- Begin expenses stockist sales -->
    <?php $stockist = $expenses->stockist; ?>
    <tr valign="top">
        <th scope="row" colspan="4">
            <small>2.</small> <strong>Stockist PV Sales Bonus</strong>
        </th>
    </tr>
    <tr title="State Stockist">
        <th scope="row">
            &mdash; <span class="medblue"><?php echo $stockist->count->state;?></span> State Stockist
        </th>
        <td>
            <tt><?php echo $stockist->pv->state;?><small>PV</small> x
                <a href="/wp-admin/admin.php?page=mc_stockist_settings#opt_stockist_sales_bonus">
                <?php echo $stockist->rates->state.$rate_symbol;?></a>
            </tt>
        </td>
        <td>
            RM <?php echo mc_currency_filter($stockist->sum_pv->state);?>
        </td>
        <td><!-- leave it blank --></td>
    </tr>
    <tr title="District Stockist">
        <th scope="row">
            &mdash; <span class="medblue"><?php echo $stockist->count->district;?></span> District Stockist
        </th>
        <td>
            <tt><?php echo $stockist->pv->district;?><small>PV</small> x
                <a href="/wp-admin/admin.php?page=mc_stockist_settings#opt_stockist_sales_bonus">
                <?php echo $stockist->rates->district.$rate_symbol;?></a>
            </tt>
        </td>
        <td>
            RM <?php echo mc_currency_filter($stockist->sum_pv->district);?>
        </td>
        <td><!-- leave it blank --></td>
    </tr>
    <tr title="Mobile Stockist">
        <th scope="row">
            &mdash; <span class="medblue"><?php echo $stockist->count->mobile;?></span> Mobile Stockist
        </th>
        <td>
            <tt><?php echo $stockist->pv->mobile;?><small>PV</small> x
                <a href="/wp-admin/admin.php?page=mc_stockist_settings#opt_stockist_sales_bonus">
                <?php echo $stockist->rates->mobile.$rate_symbol;?></a>
            </tt>
        </td>
        <td>
            RM <?php echo mc_currency_filter($stockist->sum_pv->mobile);?>
        </td>
        <td><!-- leave it blank --></td>
    </tr>
    <!-- End expenses stockist sales -->
    <!-- Begin expenses Group PV sales -->
    <tr valign="top">
        <th scope="row" colspan="4">
            <small>3.</small> <strong>Performance Level Bonus</strong>
        </th>
    </tr>
    <tr>
        <th scope="row">
            &mdash; <small>3.1.</small>Min Bonus 35PV (22.5%)
        </th>
        <td>
            <tt><?php echo $expenses->group_pv->min->count;?></tt>
        </td>
        <td>
            RM <?php echo mc_currency_filter($expenses->group_pv->min->amount);?>
        </td>
        <td><!-- leave it blank --></td>
    </tr>
    <tr>
        <th scope="row">
            &mdash; <small>3.2.</small> Max Bonus 75PV (45%)
        </th>
        <td>
            <tt><?php echo $expenses->group_pv->max->count;?></tt>
        </td>
        <td>
            RM <?php echo mc_currency_filter($expenses->group_pv->max->amount);?>
        </td>
        <td><!-- leave it blank --></td>
    </tr>
    <!-- End expenses Group PV sales -->
    <tr>
        <th scope="row"><small>4.</small> Withdrawal</th>
        <td></td>
        <td>RM 0.00</td>
        <td></td>
    </tr>
    <tr>
        <th scope="row"><small>5.</small> Administrative Expense</th>
        <td></td>
        <td>RM 0.00</td>
        <td></td>
    </tr>
    <tr>
        <th scope="row"><small>6.</small> Salaries</th>
        <td></td>
        <td>RM 0.00</td>
        <td></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2"></th>
        <th></th>
        <th>
            RM <?php echo mc_currency_filter($expenses->total);?>
        </th>
    </tr>
    </tfoot>
</table>
<?php
}

function mb_rp_cto_summary($placeholder, $options){

    /* $placeholder store post data, if within post-type page
     * not used
     */
    unset($placeholder);

    list($turnover) = $options['args'];
    $turnover = foreach_push(new stdClass(), $turnover);
?>
<table class="widefat">
    <thead>
    <tr>
        <th style="width: 44%"></th>
        <th style="width: 20%">Unit/Type</th>
        <th style="width: 20%">Subtotal</th>
        <th style="width: 16%">Amount</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th scope="row">Turnover</th>
        <td><?php echo $turnover->orders;?> Sales</td>
        <td>RM <?php echo mc_currency_filter($turnover->amount);?></td>
        <td></td>
    </tr>
    <tr>
        <th scope="row">Cost of Sales</th>
        <td></td>
        <td>RM <?php echo mc_currency_filter($turnover->cost);?></td>
        <td></td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="2">Gross Profit</th>
        <th></th>
        <th>
            RM <?php echo mc_currency_filter($turnover->gross);?>
        </th>
    </tr>
    </tfoot>
</table>
<?php

}

/**
 * report payout metabox
 */

function mb_rp_payout_table(){

    $payout = get_curmonth_registration_bonus();
    $ttl_amount = 0;
    if ($payout){
      //var_dump($payout);
    }
?>
<table id="bonus-registration" class="widefat">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Name</th>
            <th>Description</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($payout): ?>
        <?php $cnt = 1; ?>
        <?php foreach($payout as $i=> $bonus): ?>
        <tr>
            <td><?php echo $cnt; ?></td>
            <td><?php echo date('M d Y',$bonus->timestamp);?></td>
            <td><?php echo uinfo($bonus->bonus_uid). ' ('.uinfo($bonus->bonus_uid,'code').')';?></td>
            <td><?php echo $bonus->bonus_title;?></td>
            <td>RM <?php echo mc_currency_filter( (int) $bonus->bonus_value); ?></td>
        </tr>
        <?php $cnt++; ?>
        <?php $ttl_amount += (int) $bonus->bonus_value; ?>
        <?php endforeach; ?>
        <?php else: ?>
        <tr>
            <td>Sorry, there is no active transaction for this month.</td>
        </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3"></th>
            <th class="txt-right">Total</th>
            <?php if (!$payout): ?>
            <th>RM 0.00</th>
            <?php else: ?>
            <th>RM <?php echo mc_currency_filter($ttl_amount); ?></th>
            <?php endif; ?>
        </tr>
    </tfoot>
</table>
<?php if($payout): ?>
<script>
    jQuery('document').ready(function($){
        $('#bonus-registration').dataTable();

    });
</script>
<?php endif; ?>
<?php
}

/**
 * report registration metabox
 */

function mb_rp_registration_chart()
{
    mc_rp_user_month_charts();
}

function mb_rp_registration_summary($post, $options){
    unset($post);
    list($total, $month) = $options['args'];
?>
<table class="widefat sales-summary">
    <tbody>
    <tr>
        <td class="txt-right" style="width: 40%">Total User(s):</td>
        <td style="width: 60%">
            <span class="medblue"><?php echo $total['users'];?></span>
        </td>
    </tr>
    <tr>
        <td class="txt-right">Stockist :</td>
        <td>
            <span class="medblue"><?php echo $total['stockist'];?></span>
        </td>
    </tr>
    <tr>
        <th scope="row" colspan="2">This Month</th>
    </tr>
    <tr>
        <td class="txt-right">Members :</td>
        <td>
             <span class="medblue"><?php echo ($month['users'] - $month['stockist']);?></span>
        </td>
    </tr>
    <tr>
        <td class="txt-right">Stockist :</td>
        <td>
            <span class="medblue"><?php echo $month['stockist']?></span>
        </td>
    </tr>
    <tr>
        <td class="txt-right"> Total :</td>
        <td>
            <span class="medblue"><?php print $month['users'];?></span>
        </td>
    </tr>
    </tbody>
</table>

<?php
}

/**
 * report metabox
 */
function mb_rp_payout_summary()
{
    ?>
<table class="widefat sales-summary">
    <tbody>
    <tr>
        <td class="border-right"><strong>Stats</strong></td>
        <td><strong>Current Month</strong></td>
        <td><strong>Total Payout</strong></td>
    </tr>
    <tr>
        <td class="border-right"><span class="medblue"> <?php echo (get_all_sales());?></span> Sales</td>
        <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_approved_sales_month());?></span> Sales</td>
        <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_approved_sales());?></span> Sales</td>
    </tr>
    <tr>
        <td class="border-right"><?php echo get_all_pending_sales();?> <span class="medorange">Pending</span></td>
        <td><span class="medblue"><?php echo sum_all_approved_orders_month();?></span> Orders</td>
        <td><span class="medblue"><?php echo sum_all_approved_orders();?></span> Orders</td>
    </tr>
    <tr>
        <td class="border-right"><?php echo (get_all_approved_sales());?> <span class="medgreen">Total Payout</span></td>
        <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_avg_orders_month());?></span> Avg Orders</td>
        <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_avg_orders());?></span> Avg Orders</td>
    </tr>
    </tbody>
</table>

<?php
}

function mb_rp_sales_summary()
{
?>
<table class="widefat sales-summary">
    <tbody>
        <tr>
            <td class="border-right"><strong>Stats</strong></td>
            <td><strong>Current Month</strong></td>
            <td><strong>Total Income</strong></td>
        </tr>
        <tr>
            <td class="border-right"><span class="medblue"> <?php echo (get_all_sales());?></span> Sales</td>
            <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_approved_sales_month());?></span> Sales</td>
            <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_approved_sales());?></span> Sales</td>
        </tr>
        <tr>
            <td class="border-right"><?php echo get_all_pending_sales();?> <span class="medorange">Pending</span></td>
            <td><span class="medblue"><?php echo sum_all_approved_orders_month();?></span> Orders</td>
            <td><span class="medblue"><?php echo sum_all_approved_orders();?></span> Orders</td>
        </tr>
        <tr>
            <td class="border-right"><?php echo (get_all_approved_sales());?> <span class="medgreen">Closed Sales</span></td>
            <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_avg_orders_month());?></span> Avg Orders</td>
            <td><span class="medblue">RM <?php echo mc_currency_filter(sum_all_avg_orders());?></span> Avg Orders</td>
        </tr>
    </tbody>
</table>

<?php
}