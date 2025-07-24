<?php
/**
 * Plugin Name: Docty Clinic Sample Collection
 * Description: Adds a required "Sample Collection" radio field to the WooCommerce checkout page.
 * Version: 1.0
 * Author: Nimesh Gorfad
 */
 
defined('ABSPATH') || exit;
include('doctor-admin-page.php');
class Docty_Clinic_Sample_Collection {

    private $location_url = 'https://dev-backend.docty.life/api/public-api/clinic-profile/';

    public function __construct() {

        
        // Add the field to checkout
       add_action('woocommerce_after_checkout_billing_form', array($this, 'add_sample_collection_field'));
        
        // Validate the field
        add_action('woocommerce_checkout_process', array($this, 'validate_sample_collection_field'));
        
        // Save the field value to order meta
        add_action('woocommerce_checkout_create_order', array($this, 'save_sample_collection_field'), 10, 2);
         
        // Display the field value in admin
        add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'display_sample_collection_admin'), 10, 1);
        
        // Add field to checkout update
        add_filter('woocommerce_checkout_posted_data', array($this, 'include_sample_collection_in_posted_data'));
        
        // Add JavaScript for validation
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function enqueue_scripts() {
        if (is_checkout()) {
            wp_enqueue_script(
                'docty-clinic-checkout',
                plugin_dir_url(__FILE__) . 'js/checkout-validation.js',
                array('jquery', 'woocommerce'),
                '1.0.0',
                true
            );
        }
    }
    
    public function add_sample_collection_field($checkout) {
       
        echo '<div id="docty-sample-collection"><h3>' . __('Sample Collection', 'docty-clinic') . '</h3>';
        
        woocommerce_form_field('sample_collection_method', array(
            'type'     => 'radio',
            'class'    => array('form-row-wide', 'sample-collection-group'),
            'label'    => __('How would you like to provide your sample?', 'docty-clinic'),
            'required' => true,
            'default'  => 'home',
            'options'  => array(
                'home'    => __('From Home', 'docty-clinic'),
                'center'  => __('Visit At Center', 'docty-clinic')
            )
        ), $checkout->get_value('sample_collection_method'));
         echo '</div>';
        // add field for home address
        echo '<div class="sample-collection-home-address" style="">';
        woocommerce_form_field('home_address', array(
            'type'     => 'text',
            'class'    => array('form-row-wide'),
            'label'    => __('Home Address', 'docty-clinic'),
            'required' => true,
        ), $checkout->get_value('home_address'));   

       
        echo '</div>';
        // get location URL. location in json format
        // This URL is used to fetch clinic locations and staff information
        $location_url = $this->location_url;
        $clinic_profile_id = get_option('clinic_profile_id', '');
        $a_url = $location_url.$clinic_profile_id.'/?include=locations,staff';

        // Fetch clinic locations and staff information
        $locations_array = array('' => __('Select Clinic Location', 'docty-clinic'));
        $response = wp_remote_get($a_url);
        if (!is_wp_error($response)) {
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if (!empty($data)) {
                
                foreach ($data['locations'] as $location) {
                    $locations_array[$location['address']] = $location['address'];
                }
            }
        }
        // Display the locations in a select dropdown
        echo '<div class="sample-collection-clinic-address" style="display: none;">';
        woocommerce_form_field('clinic_location', array(
            'type'     => 'select',
            'class'    => array('form-row-wide'),
            'label'    => __('Select Clinic Location', 'docty-clinic'),
            'required' => true,
            'options'  => $locations_array,
        ), $checkout->get_value('clinic_location'));

        echo '</div>';
        

        // Process the data as needed
    }

    
    
    public function validate_sample_collection_field() {
        
        if (empty($_POST['sample_collection_method'])) {
            wc_add_notice(__('Please select a sample collection method.........', 'docty-clinic'), 'error');
        }
        if (isset($_POST['sample_collection_method']) && $_POST['sample_collection_method'] == 'home' && empty($_POST['home_address'])) {
            wc_add_notice(__('Please provide your home address for sample collection.', 'docty-clinic'), 'error');
        }

        
        if (isset($_POST['sample_collection_method']) && $_POST['sample_collection_method'] == 'center' && empty($_POST['clinic_location'])) {
            wc_add_notice(__('Please select a clinic location for sample collection.', 'docty-clinic'), 'error');
        }
    }

     

    
    
    public function save_sample_collection_field($order, $data) {

       $collection_method = isset($data['sample_collection_method']) ? sanitize_text_field($data['sample_collection_method']) : '';

        if (!empty($data['sample_collection_method'])) {
            $order->update_meta_data('_sample_collection_method', $collection_method);
        }
        if ($collection_method === 'home' && !empty($data['home_address'])) {
            $order->update_meta_data('_home_address', sanitize_text_field($data['home_address']));
        }
        if ($collection_method === 'center' && !empty($data['clinic_location'])) {
            $order->update_meta_data('_clinic_location', sanitize_text_field($data['clinic_location']));
        }
        $order->save();
    }
    
    public function display_sample_collection_admin($order) {
        $method = $order->get_meta('_sample_collection_method');
        if ($method) {

            echo '<div style="margin-top:15px; padding:10px; border:1px solid #ddd;">';
            $display_value = ($method == 'home') ? __('From Home', 'docty-clinic') : __('Visit At Center', 'docty-clinic');
            echo '<p><strong>' . __('Sample Collection :', 'docty-clinic') . '</strong> ' . $display_value . '</p>';
            if ($method == 'home') {
                $home_address = $order->get_meta('_home_address');
                echo '<p><strong>' . __('Home Address:', 'docty-clinic') . '</strong> ' . esc_html($home_address) . '</p>';
            } else {
                $clinic_location = $order->get_meta('_clinic_location');
                echo '<p><strong>' . __('Clinic Location:', 'docty-clinic') . '</strong> ' . esc_html($clinic_location) . '</p>';
            }
            echo '</div>';
        }
    }
    
    public function include_sample_collection_in_posted_data($data) {
        if (isset($_POST['sample_collection_method'])) {
            $data['sample_collection_method'] = sanitize_text_field($_POST['sample_collection_method']);
        }
        if (isset($_POST['home_address'])) {
            $data['home_address'] = sanitize_text_field($_POST['home_address']);
        }
        if (isset($_POST['clinic_location'])) {
            $data['clinic_location'] = sanitize_text_field($_POST['clinic_location']);
        }
        return $data;
    }
}

new Docty_Clinic_Sample_Collection();

 