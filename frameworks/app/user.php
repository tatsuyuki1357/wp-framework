<?php
namespace MyProject;

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'after_setup_theme', function() {
    global $myUser;
    $myUser = new User;
}, 8 );

/**
 *  ユーザー処理
 *
 */
class User {
    public function __construct () {
        // Eメールによるユーザー認証停止
        remove_filter( 'authenticate', 'wp_authenticate_email_password', 20, 3 );
    }
}
