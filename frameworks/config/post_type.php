<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/*
 * カスタム投稿タイプ用設定処理
 *
 */

define( 'BOOK_POST_TYPE',    'book' );
define( 'PRODUCT_POST_TYPE', 'product' );

return [
    BOOK_POST_TYPE => [
        'args' => [
            'labels' => [
                'name' => '本'
            ],
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
        ]
    ],
    PRODUCT_POST_TYPE => [
        'args' => [
            'labels' => [
                'name' => '商品'
            ],
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
        ]
    ],
];
