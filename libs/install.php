<?php
/**
 * mc_rp_install_db()
 *
 * Installation scripts
 *
 * Setup our database, this function should be
 * run once on plugin active
 *
 * @filesource https://github.com/NuarHaruha/bay-rp/blob/master/libs/install.php
 *
 * @global mixed|object $wpdb object of WP database
 * @see Wpdb::get_var() {@link http://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Variable SELECT a Variable}
 * @see Wpdb::query() {@link http://codex.wordpress.org/Class_Reference/wpdb#Run_Any_Query_on_the_Database Run Any Query on the Database}
 * @see dbDelta()
 * @see add_option()
 * @see RPTYPE
 *
 * @author  Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @filesource https://github.com/NuarHaruha/bay-rp/blob/master/libs/install.php
 * @since   1.0
 * @version 1.1
 * @return  mixed|array
 */
function mc_rp_install_db(){
    global $wpdb;

    $update = array();

    $db = RPTYPE::DB(RPTYPE::DB_SALES);

    if($wpdb->get_var("SHOW TABLES LIKE '".$db."'") != $db || RPTYPE::VERSION() < RPTYPE::DB_VERSION )
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "CREATE TABLE " . $db . " (
              id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
              month TINYINT(2) NOT NULL,
              year TINYINT(4) NOT NULL,
              status ENUM('pending','approved','cancel') NOT NULL DEFAULT 'pending',
              amount DECIMAL(13,2) NOT NULL,
              modified_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              KEY month (month),
              KEY year (year),
              KEY status (status),
              KEY amount (amount)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $update[] = dbDelta($sql); // dbDelta space sensitive

        /**
         * Report Table for user registration
         */

        $db = RPTYPE::DB(RPTYPE::DB_REGISTRATION);

        $sql = "CREATE TABLE " . $db . " (
              id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
              month TINYINT(2) NOT NULL,
              year TINYINT(4) NOT NULL,
              user_type ENUM('members','stockist','staff') NOT NULL DEFAULT 'members',
              amount DECIMAL(13,2) NOT NULL,
              modified_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              KEY month (month),
              KEY year (year),
              KEY user_type (user_type),
              KEY amount (amount)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $update[] = dbDelta($sql);

        /**
         * Report Table for user PV
         */

        $db = RPTYPE::DB(RPTYPE::DB_PV);

        $sql = "CREATE TABLE " . $db . " (
              id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
              month TINYINT(2) NOT NULL,
              year TINYINT(4) NOT NULL,
              amount DECIMAL(13,2) NOT NULL,
              modified_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              KEY month (month),
              KEY year (year),
              KEY amount (amount)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $update[] = dbDelta($sql);

        /**
         * Report Table for user withdrawal
         */

        $db = RPTYPE::DB(RPTYPE::DB_WITHDRAWAL);

        $sql = "CREATE TABLE " . $db . " (
              id BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
              month TINYINT(2) NOT NULL,
              year TINYINT(4) NOT NULL,
              status ENUM('pending','approved','cancel') NOT NULL DEFAULT 'pending',
              amount DECIMAL(13,2) NOT NULL,
              modified_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (id),
              KEY month (month),
              KEY year (year),
              KEY status (status),
              KEY amount (amount)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $update[] = dbDelta($sql);

        mc_rp_create_view();

        add_option(RPTYPE::MK_DB_VERSION, RPTYPE::DB_VERSION);
    }

    return $update;
}

function mc_rp_create_view(){

    $args = array(
        'table'     => RPTYPE::DB(RPTYPE::DB_INVOICE),
        'options'   => array('sum' => false, 'status' => 'approved'),
        'col'       => array('date'=> 'created_date', 'status' => 'order_status', 'amount' => 'total_amount')
    );

    RPTYPE::CR_VIEW('approved_sales', $args);

    $args['options']['status'] = 'pending';

    RPTYPE::CR_VIEW('pending_sales', $args);
}
/** mc_rp_install_db() */