<?php

if (!defined('ABSPATH')) exit;

if( !class_exists( 'SSBPlugin' ) ){
    class SSBPlugin{
        function __construct(){
            $this -> loaded_classes();
        }
 
        function loaded_classes(){
			require_once SSB_DIR_PATH . 'includes/ssbPlugin/inc/Init.php';
			require_once SSB_DIR_PATH . 'includes/ssbPlugin/inc/AdminMenu.php';
			require_once SSB_DIR_PATH . 'includes/ssbPlugin/inc/Enqueue.php';
			require_once SSB_DIR_PATH . 'includes/ssbPlugin/inc/ShortCode.php';
			require_once SSB_DIR_PATH . 'includes/ssbPlugin/inc/CustomColumn.php';
			require_once SSB_DIR_PATH . 'includes/ssbPlugin/inc/RestAPI.php';

			new SSB\Init();
			new SSB\AdminMenu();
			new SSB\Enqueue();
			new SSB\ShortCode();
			new SSB\CustomColumn();
			new SSB\RestAPI();
		}
        
    }
    new SSBPlugin();
}