<?php
/*
Plugin Name: MDAG Report
Plugin URI: http://mdag.my
Description: Sales report and Analysis
Version: 1.0.0
Author: Nuar, MDAG Consultancy
Author URI: http://mdag.my
License: MIT License
License URI: http://mdag.mit-license.org/
*/

/**
 *  Report
 *
 * @package     isralife
 * @category    reporting
 *
 * @author      Nuarharuha <nhnoah+bay-isra@gmail.com>
 * @copyright   Copyright (C) 2012, Nuarharuha, MDAG Consultancy
 * @license     http://mdag.mit-license.org/ MIT License
 * @filesource  http://code.mdag.my/baydura_isralife/src
 * @version     1.0
 * @access      public
 */

class report
{
    public $version             = 1.0;

    public $page                = array();

    public $cap                 = 'manage_options';

    public $slug                = 'report';

    public $plugin_path;

    public $plugin_uri;

    public $plugin_libs;

    public $plugin_public_url;

    public function __construct()
    {
        $this->_init();
    }

    private function _load_default_filesystem()
    {
        $this->plugin_uri           = plugin_dir_url(__FILE__);
        $this->plugin_path          = plugin_dir_path(__FILE__);
        $this->plugin_libs          = $this->plugin_path.'libs/';
        $this->plugin_public_url    = $this->plugin_uri.'public/';

        $includes = array('type','install','query','metabox','registration','payout','cto');

        foreach($includes as $f){
            require $this->plugin_libs.$f.'.php';
        }

    }

    private function _init()
    {
        $this->_load_default_filesystem();

        if (is_admin()){
            $this->_register_admin_settings();
        }
    }

    private function _register_admin_settings()
    {
        add_action('admin_init', array(&$this, 'register_admin_stylesheets'));
        add_action('admin_init', array(&$this, 'register_admin_scripts'));
        add_action('admin_menu', array(&$this, 'register_admin_menus'));
        add_action('add_meta_boxes', array(&$this,'register_metabox'));
    }

    public function register_admin_stylesheets()
    {
        wp_register_style('report-styles', $this->plugin_public_url.'styles.css', array('font-awesome'));
    }

    public function admin_stylesheets()
    {
        wp_enqueue_style('report-styles');
    }


    public function register_admin_scripts()
    {
        wp_register_script('data-table', $this->plugin_public_url.'dtable.min.js',array('jquery'),false,true);
    }

    public function admin_scripts()
    {
        wp_enqueue_script('data-table');
        wp_enqueue_script('jqprint');
    }

    public function admin_footer_scripts()
    {
        $scripts = "jQuery(document).ready(function($){
           $('.go-back').click(function(e){
                e.preventDefault(); window.history.go(-1); });
                postboxes.add_postbox_toggles(pagenow);
        });";
        t('script',$scripts);
    }


    private function _page_setup($hook)
    {
        add_action('admin_print_styles-'.$hook, array(&$this,'admin_stylesheets') );
        add_action('admin_print_styles-'.$hook, array(&$this,'admin_scripts') );

        add_action('load-'.$hook, array($this,'page_actions'),9);
        add_action('load-'.$hook, array($this,'save_settings'),10);
        add_action('admin_footer-'.$hook,array(&$this,'admin_footer_scripts'));
    }

    public function page_actions()
    {
        $page = (isset($_REQUEST['panel']) ) ? $_REQUEST['panel'] : $_REQUEST['page'];
        $page   = 'report_page_'.$page;

        do_action('add_meta_boxes_'.$page, null);
        do_action('add_meta_boxes', $page, null);

        add_screen_option('layout_columns', array('max' => 3, 'default' => 2) );

        wp_enqueue_script('jquery');
        wp_enqueue_script('postbox');
        wp_enqueue_script('highcharts');
    }

    public function save_settings()
    {}

    public function register_admin_menus()
    {
        $title      = 'Reports';
        $callback   = array(&$this,'load_panel');
        $icon       = $this->plugin_uri.'img/report-16.png';
        $pos        = 200;

        $this->page['primary'] = add_menu_page($title, $title, $this->cap, $this->slug, $callback, $icon, $pos);
        $this->_page_setup($this->page['primary']);

        $title      = 'Registration';

        $this->page['register'] = add_submenu_page($this->slug, $title, $title, $this->cap,'report-registration', $callback);

        $this->_page_setup($this->page['register']);

        $title      = 'Bonus Payout';

        $this->page['payout'] = add_submenu_page($this->slug, $title, $title, $this->cap,'report-payout', $callback);

        $this->_page_setup($this->page['payout']);

        $title      = 'Turnover';

        $this->page['cto'] = add_submenu_page($this->slug, $title, $title, $this->cap,'report-cto', $callback);

        $this->_page_setup($this->page['cto']);
    }

    public function register_metabox()
    {
        switch($_REQUEST['page']){
            case 'report':
                $this->register_page_report_metabox();
                break;
            case 'report-registration':
                $this->register_page_report_registration_metabox();
                break;
            case 'report-payout':
                $this->register_page_report_payout_metabox();
                break;
            case 'report-cto':
                $this->register_page_report_cto_metabox();
                break;
        }
    }

    /**
     * register metabox on report members cto page
     *
     * @see add_metabox()
     * @uses add_metabox() WP function to create custom metabox
     * @return void
     */
    public function register_page_report_cto_metabox()
    {

        /* date of current month */
        $date           = date("M Y");

        $args = array(
            get_sales_turnover_curmonth(),  // turnover data
            get_sales_expenses_curmonth(),  // expenses
            get_sales_shared_cto_curmonth() // shared cto
        );

        add_meta_box('opt_report_cto_tbl','Sales Turnover for the month of '.$date, 'mb_rp_cto_summary',
            $this->page['cto'],'normal','high', $args);

        add_meta_box('opt_report_cto_expense','Expenses for the month of '.$date, 'mb_rp_expenses_summary',
            $this->page['cto'],'normal','high', $args);

        add_meta_box('opt_report_cto_expense_summary','Shared CTO Summary for the month of '.$date, 'mb_rp_expense_cto_summary',
            $this->page['cto'],'normal','high', $args);

        add_meta_box('opt_report_summary','Total Summary', 'mb_rp_cto_amount_summary',
            $this->page['cto'],'side','high', $args);
    }

    /**
     * register metabox on report members payout page
     *
     * @see add_metabox()
     * @uses add_metabox() WP function to create custom metabox
     * @return void
     */
    public function register_page_report_payout_metabox()
    {
        add_meta_box('opt_report_payout_tbl','Registration Bonus', 'mb_rp_payout_table',
            $this->page['payout'],'normal','high');
    }

    /**
     * register metabox on report members registration page
     *
     * @see add_metabox()
     * @uses add_metabox() WP function to create custom metabox
     * @return void
     */
    public function register_page_report_registration_metabox()
    {

        $total = array(
                'users'     => mc_rp_get_all_users_count(),
                'stockist'  => mc_rp_get_all_stockist_count() );
        $month = array(
                'users' => mc_rp_get_total_user_curmonth(),
                'stockist' => mc_rp_get_total_stockist_curmonth() );
        $args = array($total, $month);


        add_meta_box('opt_report_register_summary','Summary', 'mb_rp_registration_summary',
            $this->page['register'],'side','high', $args);

        add_meta_box('opt_report_register_chart','Registration Chart', 'mb_rp_registration_chart',
            $this->page['register'],'normal','high');
    }

    /**
     * register metabox on report page
     *
     * @see add_metabox()
     * @uses add_metabox() WP function to create custom metabox
     * @return void
     */
    public function register_page_report_metabox()
    {
        add_meta_box('opt_report_chart','Sales Chart', 'mc_sales_charts', $this->page['primary'],'normal','high');
        add_meta_box('opt_report_summary','Sales Summary', 'mb_rp_sales_summary', $this->page['primary'],'normal','high');
    }

    /**
     * Include plugin page file
     *
     * @global string   $_REQUEST['page']   page now
     * @return void
     */
    public function load_panel()
    {
        if (isset($_REQUEST['page'])){

            switch($_REQUEST['page']){
                case 'report-cto':          $file = 'cto'; break;
                case 'report-registration': $file = 'registration'; break;
                case 'report-payout':       $file = 'payout'; break;
                case 'report': default:     $file = 'main'; break;
            }

            require_once $this->plugin_path. sprintf('panels/%s.php', $file);
        }
    }
}

new report();

/**
 * plugin setup installation, run once
 */
register_activation_hook( __FILE__ , 'mc_rp_setup');
function mc_rp_setup(){ mc_rp_install_db();}