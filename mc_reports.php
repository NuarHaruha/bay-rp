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
        $this->plugin_uri   = plugin_dir_url(__FILE__);

        $this->plugin_path  = plugin_dir_path(__FILE__);

        $this->plugin_libs  = $this->plugin_path.'libs/';

        $this->plugin_public_url = $this->plugin_uri.'public/';

        $this->_init();
    }

    private function _load_default_filesystem()
    {
        $includes = array('type','query','metabox');
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

    }

    public function admin_scripts()
    {

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

        $title      = 'Payout';

        $this->page['payout'] = add_submenu_page($this->slug, $title, $title, $this->cap,'report-payout', $callback);

        $this->_page_setup($this->page['payout']);

        $title      = 'CTO';

        $this->page['cto'] = add_submenu_page($this->slug, $title, $title, $this->cap,'report-cto', $callback);

        $this->_page_setup($this->page['cto']);
    }

    public function register_metabox()
    {
        switch($_REQUEST['page']){
            case 'report':
                $this->register_page_report_metabox();
                break;
        }
    }

    public function register_page_report_metabox()
    {

        add_meta_box('opt_report_chart','Sales Chart', 'mc_sales_charts', $this->page['primary'],'normal','high');
        add_meta_box('opt_report_summary','Sales Summary', 'mb_rp_sales_summary', $this->page['primary'],'normal','high');
    }

    public function load_panel()
    {
        if (isset($_REQUEST['page'])){
            switch($_REQUEST['page']){
                case 'report':
                    require_once $this->plugin_path.'panels/main.php';
                    break;
            }
        }
    }
}

new report();