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
 * report payout metabox
 */

function mb_rp_payout_table(){

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