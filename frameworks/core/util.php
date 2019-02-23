<?php
namespace YuyaTajima;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *  汎用ユーティリティ処理群
 *
 * APP_CONFIG_PATH定数が定義ずみである必要がある
 *
 */
class Util {

    /**
     * 設定ファイルの内容を取得する
     *
     * @param string $file_name ファイル名
     * @return mixed 指定したファイルのreturn文の返り値
     */
    public static function read_config ( string $file_name ) {
        if ( ! is_readable( APP_CONFIG_PATH . '/' . $file_name ) ) {
            return false;
        }
        return include APP_CONFIG_PATH . '/' . $file_name;
    }

    /**
     * 設定ファイルを読み込む
     *
     * @param string $file_name ファイル名
     */
    public static function load_config ( string $file_name ) {
        if ( ! is_readable( APP_CONFIG_PATH . '/' . $file_name ) ) {
            return false;
        }
        require_once APP_CONFIG_PATH . '/' . $file_name;
    }
}
