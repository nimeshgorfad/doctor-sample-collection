<?php
/*
 * Admin Page for Docty Clinic Sample Collection
 * This option page has the functionality to add clinic profile id
 */

// Hook to add admin menu
add_action('admin_menu', 'docty_collection_setting_menu');

function docty_collection_setting_menu() {
    add_menu_page(
        'Collection Setting', // Page title
        'Collection Setting', // Menu title
        'manage_options',     // Capability
        'docty-collection-setting', // Menu slug
        'docty_collection_setting_page', // Callback function
        'dashicons-admin-generic', // Icon
        80 // Position
    );
}

function docty_collection_setting_page() {
    // Save option if form is submitted
    if (isset($_POST['docty_collection_setting_submit'])) {
        if (isset($_POST['clinic_profile_id'])) {
            $clinic_profile_id = sanitize_text_field($_POST['clinic_profile_id']);
            update_option('clinic_profile_id', $clinic_profile_id);
            echo '<div class="updated"><p>Clinic Profile ID saved.</p></div>';
        }
    }
    $saved_id = get_option('clinic_profile_id', '');
    ?>
    <div class="wrap">
        <h1>Collection Setting</h1>
        <form method="post">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="clinic_profile_id">Clinic Profile ID</label></th>
                    <td><input type="text" id="clinic_profile_id" name="clinic_profile_id" value="<?php echo esc_attr($saved_id); ?>" class="regular-text" /></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="docty_collection_setting_submit" class="button-primary" value="Save Changes" />
            </p>
        </form>
    </div>
    <?php
}
 