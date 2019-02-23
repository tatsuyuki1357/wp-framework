<?php
namespace MyProject;

use YuyaTajima\Util;

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'after_setup_theme', function() {
    //外部に公開しないが、必要に応じて公開する
    $init = new Init;
}, 5 );

/**
 *  初期化処理全般を管理、実行する
 *
 */
class Init {

    public function __construct () {
        add_action( 'init', [$this, 'register_post_type'], 5 );
    }

    /**
     * カスタム投稿タイプの登録を行う
     *
     * 設定内容はAPP_CONFIG_PATH内のpost_type.phpに記載する
     */
    public function register_post_type () {
        $settings = Util::read_config('post_type.php');
        if ( ! $settings ) {
            return;
        }
        // デフォルト設定
        $defaults = [
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => false,
            'show_in_menu'       => false,
            'query_var'          => false,
            'hierarchical'       => false,
            'supports'           => ['title', 'editor'],
            'show_in_rest'       => true,
        ];
        foreach ( $settings as $post_type => $args ) {
            register_post_type( $post_type , array_merge($defaults, $args['args']) );
        }
    }
}
