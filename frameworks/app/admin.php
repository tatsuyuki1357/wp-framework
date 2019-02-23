<?php
namespace MyProject;

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'after_setup_theme', function() {
    global $myAdmin;
    $myAdmin = new Admin;
}, 10 );

/**
 *  管理画面処理
 *
 */
class Admin {

    public function __construct () {
        add_action( 'restrict_manage_posts', [$this, 'add_time_field'],10 ,2 );
    }

    /**
     *  投稿一覧テーブルのnavエリアに現在時刻を表示する
     *
     *  @param string $post_type 投稿タイプ名
     *  @param string $which     リストテーブルに対しtえ追加マークアップを行う場所 'top' or 'bottom'
     */
    public function add_time_field ( $post_type, $which ) {
        if ( 'post' === $post_type ) {
            $time = sprintf('%s',  current_time('Y-m-d H:i:s'));
            echo sprintf('<div class="alignleft">%s</div>', esc_html($time));
        }
    }
}
