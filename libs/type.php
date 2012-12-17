<?php
/**
 * RPTYPE
 *
 * @package     isralife
 * @category    reporting
 *
 * @author      Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @copyright   Copyright (C) 2012, Nuarharuha, MDAG Consultancy
 * @license     http://mdag.mit-license.org/ MIT License
 * @filesource  http://code.mdag.my/baydura_isralife/src
 * @version     1.1
 * @access      public
 */
final class RPTYPE
{
    /**
     * Database version
     * @var float
     * */
    const DB_VERSION                        = 1.1;

    /**
     * Table Report Sales
     * @var string
     */
    const DB_SALES                          = 'mc_rp_sales';

    /**
     * Table Report User registration bonus
     * @var string
     */
    const DB_REGISTRATION                   = 'mc_rp_users';

    /**
     * Table Report User PV Bonus
     * @var string
     */
    const DB_PV                             = 'mc_rp_pv';

    /**
     * Table Report for User Withdrawal
     * @var string
     */
    const DB_WITHDRAWAL                     = 'mc_rp_withdrawal';

    /**
     * Product Invoice table
     * @var string
     */
    const DB_INVOICE                        = 'mc_invoices';

    /**
     * Database version meta key
     * @var string
     */
    const MK_DB_VERSION                     = 'mc_rp_db_version';

    /**
     *  MYSQL function for select the week of month
     *  %s valid MYSQL DATETIME
     * @var string
     */
    const SQL_FORMAT_WEEK_MONTH             = 'CEILING(DAYOFMONTH(%s)/7)';

    const SQL_MONTH_YEAR                    = 'DATE_FORMAT(%1$s,"%%m-%%y")=DATE_FORMAT(NOW(),"%%m-%%y")';

    const SQL_PREFIX_VIEW                   = 'v_';

    /**
     * shared cto amount
     * @var int
     */
    const SHARED_CTO                        = 12;

    const SHARED_CTO_STOCKIST               = 4;

    const SHARED_CTO_LEADERS                = 8;

    public function __construct(){ return; }

    /**
     * @uses $wpdb wp database object
     * @author Nuarharuha <nhnoah+bay-isra@gmail.com>
     * @since 0.1
     *
     * @param string $name const of RPTYPE::DB_{$}
     * @return string db table name with base prefix (multi site prefix)
     */
    public static function DB($name)
    {   global $wpdb;
        return $wpdb->base_prefix.$name;
    }

    /**
     * @return int|float db version
     */
    public static function VERSION()
    {
        return (float) get_option(self::MK_DB_VERSION);
    }

    /**
     * return the current month clause query
     * @param string $date_field valid MYSQL date time
     * return string
     */
    public static function CL_CURMONTH($date_field)
    {
       return sprintf(self::SQL_MONTH_YEAR,$date_field);
    }

    public static function CR_VIEW($view_name, $args = array(
                                    'table'     => 'wp_mc_invoices',
                                    'options'   => array(
                                        'status'    => 'approved',
                                        'sum'       => false),
                                    'col'       => array(
                                        'date'      => 'created_date',
                                        'status'    => 'order_status',
                                        'amount'    => 'total_amount'
                                    )
    ))
    {   global $wpdb;

        $view   = self::SQL_PREFIX_VIEW.$view_name;
        $table  = $args['table'];
        $date   = sprintf('%s.%s', $args['table'], $args['col']['date']);
        $ttl    = sprintf('%s.%s', $args['table'], $args['col']['amount']);
        $status = sprintf('%s.%s', $args['table'], $args['col']['status']);
        $type   = $args['options']['status'];
        $ttl    = (!empty($args['options']['sum']) ) ? "SUM($ttl)" : $ttl;

        $sql = "CREATE VIEW $view AS SELECT $date AS date, CEILING(DAYOFMONTH($date)/7) AS week_month, DATE_FORMAT($date,'%m-%y') AS month_year, $ttl AS amount FROM $table WHERE ($status = '$type')";

        $wpdb->query($sql);
    }
}