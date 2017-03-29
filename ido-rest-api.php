<?php
/**
 * IDO REST API
 *
 * @package             IDO REST API
 * @author              Fred Hong <fred@idomedia.ca>
 *
 * @wordpress-plugin
 * Plugin Name:         IDO REST API
 * Description:         Customize REST API for 52calgary
 * Version:             0.0.1
 * Author:              Fred Hong
 * Author URI:          https://fredhong.ca
 */

if(!defined('WPINC')){
    exit('Do Not access this file directly: ' . basename(__FILE__));
}

if(!defined('IDO_REST_VERSION')){
    define('IDO_REST_VERSION', '0.0.1');
}

if(!class_exists('Ido_REST')){
    class Ido_REST{
        private static $instance;

        public static function initialize(){
            if(!defined('REST_API_VERSION')){
                return;
            }
            if(!isset(self::$instance)&&!(self::$instance instanceof Ido_REST)){
                self::$instance = new Ido_REST;
                self::$instance->actions();
            }

            self::$instance->define_constants();
            return self::$instance;
        }

        public function define_constants(){
            define('IDO_REST_PLUGIN_DIR', plugin_dir_path(__FILE__));
            define('IDO_REST_PLUGIN_URL', plugin_dir_url(__FILE__));
            define('IDO_REST_SLUG','ido');
            define('IDO_REST_API_VERSION', '1');
        }

        private function actions(){
            add_action('plugins_loaded', array(&$this, 'plugins_loaded'));

            register_activation_hook( __FILE__, array( 'Ido_REST', 'activation' ) );

            register_deactivation_hook( __FILE__, array( 'Ido_REST', 'deactivation' ) );

            register_uninstall_hook( __FILE__, array( 'Ido_REST', 'uninstall' ) );

            // add_filter( 'register_post_type_args', array($this, 'add_show_in_rest'), 11, 2 );

            add_action('rest_api_init', array($this, 'setup_ido_rest'));
        }

        public function plugins_loaded(){

            $this->includes();

        }

        public function includes(){
            require_once(IDO_REST_PLUGIN_DIR . 'ido-rest-hooks.php');
            require_once(IDO_REST_PLUGIN_DIR . 'ido-rest-helper-functions.php');
            if(!is_admin()){
                if(!function_exists('is_plugin_active')){
                    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }

                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-posts-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-places-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-adverts-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-topics-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-users-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-terms-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-comments-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-attachments-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-ads-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-resources-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-replies-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-forms-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-activities-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-registrations-controller.php');
                require_once(IDO_REST_PLUGIN_DIR . 'includes/class-ido-rest-messages-controller.php');             



                require_once(IDO_REST_PLUGIN_DIR . 'ido-rest-addtional-fields.php');
            }   
        }

        public function setup_ido_rest(){
            if(defined('REST_REQUEST') && REST_REQUEST){
                $this->setup_ido_rest_endpoints();
            }
        }

        private function setup_ido_rest_endpoints(){

            $controller = new Ido_REST_Posts_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Places_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Adverts_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Topics_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Users_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Terms_Controller('place_category');
            $controller->register_routes();

            $controller = new Ido_REST_Terms_Controller('advert_category');
            $controller->register_routes();

            $controller = new Ido_REST_Terms_Controller('topic-tag');
            $controller->register_routes();

            $controller = new Ido_REST_Terms_Controller('area');
            $controller->register_routes();

            $controller = new Ido_REST_Comments_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Attachments_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Ads_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Resources_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Replies_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Forms_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Activities_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Registrations_Controller();
            $controller->register_routes();

            $controller = new Ido_REST_Messages_Controller();
            $controller->register_routes();
        }

        public static function activation(){
            self::idocalgary_create_tables();
        }

        public static function deactivation(){
            self::idocalgary_truncate_tables();
        }

        public static function uninstall(){
            self::idocalgary_delete_tables();

        }

        public static function idocalgary_create_tables(){
            global $wpdb;

            $wpdb->hide_errors();
            $collate='';
            if($wpdb->has_cap('collation')){
                if (!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
                if (!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";             
            }

            $IDO_CALGARY_LOCATION_TABLE = "CREATE TABLE IF NOT EXISTS wp_locations " . " (
                id int(11) AUTO_INCREMENT NOT NULL,
                post_id int(11) NOT NULL UNIQUE,
                latitude DECIMAL(10, 8) NOT NULL,
                longitude DECIMAL(11, 8) NOT NULL,
                address varchar(254) NOT NULL,
                PRIMARY KEY (id)
                ) $collate";
            
            $wpdb->query($IDO_CALGARY_LOCATION_TABLE);
        }

        public static function idocalgary_truncate_tables(){
            
            global $wpdb;

            $wpdb->query("TRUNCATE TABLE wp_locations");
        }

        public static function idocalgary_delete_tables(){
            
            global $wpdb;
            
            $wpdb->query("DROP TABLE IF EXISTS wp_locations");
        }
    }
}

global $ido_rest;
$ido_rest = new Ido_REST();
$ido_rest->initialize();