<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// 開発者ツール
require_once __DIR__ . '/plugins/develop.php';
require_once __DIR__ . '/plugins/console_log.php';

// アプリ設定ファイルまでのディレクトリパス
define( 'APP_CONFIG_PATH', __DIR__ . '/config' );

// 汎用ユーティリティライブラリ
require_once __DIR__ . '/core/util.php';

// アプリ初期化処理
require_once __DIR__ . '/app/init.php';
// ユーザー処理
require_once __DIR__ . '/app/user.php';
// 管理画面処理
require_once __DIR__ . '/app/admin.php';
