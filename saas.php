<?php  
/**
 * Plugin Name:       Software SaaS with JWT
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Easy solution to give SaaS facility to any cloud based Software using jwt token. "Woocommerce" and "JWT Auth – WordPress JSON Web Token Authentication" plugin is required to use this plugin.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Md Tanzim Khan
 * Author URI:        https://facebook.com/oddvoots
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       saas
 * Domain Path:       /languages
 */

if( file_exists( dirname(__FILE__).'/classes/websites.php' ) ){
    require_once( dirname(__FILE__).'/classes/websites.php' );
}
if( file_exists( dirname(__FILE__) . '/inc/codestar-framework/codestar-framework.php') ){
    require_once( dirname(__FILE__) . '/inc/codestar-framework/codestar-framework.php' );
}
if( file_exists( dirname(__FILE__) . '/inc/codestar-framework/custom.php') ){
    require_once( dirname(__FILE__) . '/inc/codestar-framework/custom.php' );
}