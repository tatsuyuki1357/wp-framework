<?php
namespace YuyaTajima;

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'after_setup_theme', function() {
    global $WpDevUtil;
    $WpDevUtil = new WpDevUtil;
}, 100 );

/**
 *  WordPress開発時に便利な機能を提供する
 *
 *  WordPressの開発用途であれば、テーマや環境に依存せず動作する
 *  また、いかなる状況で無効化しても問題ない
 */
class WpDevUtil {

    // アドミニバーに表示される開発者情報の識別子
    private const ADMIN_BAR_ID     = 'develop';
    // アドミニバーに開発者情報として表示されるタイトル
    private const ADMIN_BAR_TITLE  = '開発者情報';
    // 開発者情報に表示されるSQL情報の識別子
    private const ADMIN_BAR_SQL_ID = 'sql';
    // 開発者情報に表示されるエラー情報の識別子
    private const ADMIN_BAR_ERROR_ID = 'error';

    // 処理計測タイマー開始時の時刻を記録
    private $timer_start = null;
    // 実際に処理にかかったミリ秒数を記録
    private $timer       = null;

    public function __construct () {
        // 開発者メニューをカスタマイズCSSの為の処理
        add_action( 'wp_head',            [ $this, 'add_styles_for_admin_bar' ], 10 );
        add_action( 'admin_print_styles', [ $this, 'add_styles_for_admin_bar' ], 10 );

        // アドミニバーに開発者メニューを表示する処理
        add_action( 'admin_bar_menu',  [ $this, 'admin_bar_develop_menu' ], 85 );
        // 計測時間を開始する処理
        add_filter( 'posts_pre_query', [ $this, 'timer_start' ], 9999, 2 );
        // 計測時間を終了する処理
        add_filter( 'posts_results',   [ $this, 'timer_end' ], 1, 2 );
        // あまりないケースだが、posts_resultsのフィルターを通る前に処理が返されてしまった場合、計測処理を終了する処理
        add_filter( 'wp',              [ $this, 'timer_end_wp' ], 1, 1 );
    }

    /**
     * クエリの実行時間のタイマーを開始する
     *
     * @param null|string $null ユーザーが独自でSQLを追加していた場合はSQL文
     * @param WP_Query $query WP_Queryのインスタンス (passed by reference).
     * @return null
     */
    public function timer_start ( $null, $query ) {
        if ( $query->is_main_query() ) {
            $this->timer_start = microtime( true );
        }
        return $null;
    }

    /**
     * クエリの実行が終わった直後、タイマーを設定する
     *
     * @param array $posts
     * @param WP_Query $query WP_Queryのインスタンス (passed by reference).
     *
     */
    public function timer_end ( $posts, $query ) {
        if ( $query->is_main_query() ) {
            $this->timer_set();
        }
        return $posts;
    }

    /**
     * self::timer_end()が実行されないケースの時に、タイマーを設定する
     *
     * 基本的に全てのメインクエリはself::timer_end()を通るが、
     * ごく一部のクエリは通らない為、必ず計測時間を取得する為の措置
     *
     */
    public function timer_end_wp ( $wp ) {
        $this->timer_set();
    }

    /**
     * クエリの実行時間を設定する
     *
     */
    private function timer_set () {
        if ( ! is_null($this->timer) ) {
            return;
        }
        $timer        = microtime( true ) - $this->timer_start;
        $msec         = $timer * 1000;
        $format_msec  = sprintf('%0.3f', $msec);
        $this->timer = $format_msec;
    }

    /**
     * アドミニバーに開発者情報を表示する処理を行う
     *
     * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Barのインスタンス, passed by reference
     */
    public function admin_bar_develop_menu ( $wp_admin_bar ) {
        $this->init_develop_menu( $wp_admin_bar );
        $this->add_sql_menu( $wp_admin_bar );
        $this->add_error_menu( $wp_admin_bar );
    }

    /**
     * アドミニバーに開発者情報を登録するための初期化処理
     *
     * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Barのインスタンス, passed by reference
     */
    private function init_develop_menu ( $wp_admin_bar ) {
        $wp_admin_bar->add_menu([
            'id'    => self::ADMIN_BAR_ID,
            'title' => self::ADMIN_BAR_TITLE,
        ]);
    }

    /**
     * アドミニバーの開発者情報にSQL情報を追加する
     *
     * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Barのインスタンス, passed by reference
     */
    private function add_sql_menu ( $wp_admin_bar ) {
        global $wp_query;

        // メインクエリが定義されていない時のメッセージ
        $main_sql = 'main query does not exist.';
        if ($wp_query->request) {
            $main_sql = $this->format_sql_for_html( $wp_query->request ) . '<br/> execution time : ' . $this->timer . ' (msec)';
        }

        $wp_admin_bar->add_menu([
            'parent' => self::ADMIN_BAR_ID,
            'id'     => self::ADMIN_BAR_SQL_ID,
            'title'  => $main_sql,
        ]);
    }

    /**
     * アドミニバーの開発者情報にエラー情報を追加する
     *
     * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Barのインスタンス, passed by reference
     */
    private function add_error_menu ( $wp_admin_bar ) {

        $error_msg = '<span class="no-error">No errors exist.</span>';
        $last_error = error_get_last();
        if ( $last_error ) {
            $error_msg = sprintf( '<span class="error"><strong>%s</strong> <br /> on line number %d in %s</span>'
                ,$last_error['message']
                ,$last_error['line']
                ,$last_error['file']
            );
        }

        $wp_admin_bar->add_menu([
            'parent' => self::ADMIN_BAR_ID,
            'id'     => self::ADMIN_BAR_ERROR_ID,
            'title'  => $error_msg,
        ]);
    }

    /**
     * アドミニバー用のCSSを出力する
     *
     */
    public function add_styles_for_admin_bar () {
        if ( ! is_user_logged_in() ) {
            return;
        }
    ?>
    <style>
        #wpadminbar .quicklinks #wp-admin-bar-<?php echo self::ADMIN_BAR_ID; ?>.menupop.hover ul li .ab-item {
            height:auto;
        }
        #wpadminbar .quicklinks #wp-admin-bar-<?php echo self::ADMIN_BAR_ID; ?> strong {
            font-weight:bold;
        }
        #wpadminbar .quicklinks #wp-admin-bar-<?php echo self::ADMIN_BAR_ID; ?> .error {
            color:red;
        }
        #wpadminbar .quicklinks #wp-admin-bar-<?php echo self::ADMIN_BAR_ID; ?> .no-error {
            color:green;
        }
    </style>
    <?php
    }

    /**
     * HTML出力用にSQLをフォーマットする
     *
     * @param string $sql SQL文として有効な文字列
     * @return string
     */
    private function format_sql_for_html ( string $sql ) {
        return preg_replace( '#\s(FROM|WHERE|AND|OR|ORDER\sBY|LIMIT)\s#i', '<br/>${1} ', $sql );
    }
}

