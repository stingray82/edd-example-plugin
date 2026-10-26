<?php
/**
 * Plugin Name:       Example Plugin
 * Description:       A test plugin demonstrating UUPD_Updater integration.
 * Tested up to:      7.1.3
 * Requires at least: 6.5
 * Requires PHP:      8.0
 * Version:           1.0.0.8
 * Author:            Nathan Foley
 * Author URI:        https://reallyusefulplugins.com
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       example-plugin
 * Website:           https://reallyusefulplugins.com
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'UUPD_TDLAB_EXAMPLE_PLUGIN_VENDOR', 'tdlab' );
define('UUPD_TDLAB_EXAMPLE_PLUGIN_VERSION', '1.0.0.8');
define( 'UUPD_TDLAB_EXAMPLE_PLUGIN_MAIN_FILE', __FILE__ );
define( 'UUPD_TDLAB_EXAMPLE_PLUGIN_ITEM_NUMBER', 296 );
define( 'UUPD_TDLAB_EXAMPLE_PLUGIN_FRIENDLY_NAME', 'Example Plugin' );
define( 'UUPD_TDLAB_EXAMPLE_PLUGIN_SLUG', 'example-plugin' );
define( 'UUPD_TDLAB_EXAMPLE_PLUGIN_SERVER_BASE', 'https://edddeploy.tdlab.uk/' );
define( 'UUPD_TDLAB_EXAMPLE_PLUGIN_MENU_SLUG', 'tdlab-example-plugin' );

add_action( 'plugins_loaded', function() {
    require_once __DIR__ . '/inc/updater.php';
    require_once __DIR__ . '/inc/class-uupd-license-ui.php';

    \UUPD\UI\License\V1\UUPD_License_UI::register([
        'vendor'         => UUPD_TDLAB_EXAMPLE_PLUGIN_VENDOR,
        'slug'           => UUPD_TDLAB_EXAMPLE_PLUGIN_SLUG,
        'item_id'        => UUPD_TDLAB_EXAMPLE_PLUGIN_ITEM_NUMBER,
        'license_server' => UUPD_TDLAB_EXAMPLE_PLUGIN_SERVER_BASE,
        'metadata_base'  => UUPD_TDLAB_EXAMPLE_PLUGIN_SERVER_BASE . 'wp-json/uupd/v1/metadata',
        'plugin_name'    => UUPD_TDLAB_EXAMPLE_PLUGIN_FRIENDLY_NAME,
        'menu_parent'    => 'top_level',
        'menu_slug'      => UUPD_TDLAB_EXAMPLE_PLUGIN_MENU_SLUG,
        'page_title'     => UUPD_TDLAB_EXAMPLE_PLUGIN_FRIENDLY_NAME . ' License',
        'menu_title'     => UUPD_TDLAB_EXAMPLE_PLUGIN_FRIENDLY_NAME . ' License',
        'capability'     => 'manage_options',
    ]);

    \UUPD\V2\UUPD_Updater_V2::register([
        'vendor'      => UUPD_TDLAB_EXAMPLE_PLUGIN_VENDOR,
        'plugin_file' => plugin_basename( UUPD_TDLAB_EXAMPLE_PLUGIN_MAIN_FILE ),
        'slug'        => UUPD_TDLAB_EXAMPLE_PLUGIN_SLUG,
        'name'        => UUPD_TDLAB_EXAMPLE_PLUGIN_FRIENDLY_NAME,
        'version'     => UUPD_TDLAB_EXAMPLE_PLUGIN_VERSION,
        'mode'        => 'json',
        'server'      => UUPD_TDLAB_EXAMPLE_PLUGIN_SERVER_BASE . 'wp-json/uupd/v1/metadata',
    ]);
}, 1 );


// 1. Add the settings page to the admin menu
add_action('admin_menu', 'esp_add_settings_page');
function esp_add_settings_page() {
    add_options_page(
        'Example Setting Page',     // Page title
        'Example Setting Page',     // Menu title
        'manage_options',           // Capability
        'esp-example-setting-page', // Menu slug
        'esp_render_settings_page'  // Callback
    );
}

// 2. Register the setting
add_action('admin_init', 'esp_register_settings');
function esp_register_settings() {
    register_setting(
        'esp_settings_group', // Option group
        'esp_text_option'     // Option name (stored in wp_options)
    );
}

// 3. Render the settings page
function esp_render_settings_page() {
    // Check capability
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>

    <div class="wrap">
        <h1>Example Setting Page</h1>

        <?php \UUPD\UI\License\V1\UUPD_License_UI::render_box_for( UUPD_TDLAB_EXAMPLE_PLUGIN_SLUG, UUPD_TDLAB_EXAMPLE_PLUGIN_VENDOR ); ?>


        <form method="post" action="options.php">
            <?php
            // Output security fields for the registered setting
            settings_fields('esp_settings_group');

            // If you had sections, you'd call do_settings_sections() here
            // do_settings_sections('esp-example-setting-page');

            // Get existing value
            $text_value = get_option('esp_text_option', '');
            ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="esp_text_option">Example Text Field</label>
                    </th>
                    <td>
                        <input
                            type="text"
                            id="esp_text_option"
                            name="esp_text_option"
                            value="<?php echo esc_attr($text_value); ?>"
                            class="regular-text"
                        />
                        <p class="description">Enter any text you like.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>

    <?php
}
