<?php
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