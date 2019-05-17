<?php
/**
 *
 * @since      2.04
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRM_Debug {

    /**
     * @param $wp_debug_backtrace
     * @return array
     * @since 2.04
     */
    public static function _get_backtrace_arr($wp_debug_backtrace) {
        $wp_debug_backtrace = array_filter($wp_debug_backtrace, function ($arr_val) {
            if ( in_array($arr_val,
                ['require_once(\'wp-settings.php\')',
                    'require_once(\'wp-config.php\')',
                    'require_once(\'wp-load.php\')',
                    'require(\'wp-blog-header.php\')'] )
            ) {
                return null;
            }
            return $arr_val;
        });

        return $wp_debug_backtrace;
    }

    /**
     * @param Exception $exception
     * @since 2.04
     */
    public static function _admin_global_exception_handler( $exception ) {

        $file_path = str_replace([ABSPATH, 'wp-content'], '', $exception->getFile());
        lrm_log( 'LRM AJAX error', $exception->getMessage() . ' in ' . $file_path );

        echo 'Can\'t process this request, the error happens in file <strong>' . $file_path . '</strong> on line ' . $exception->getLine();

        echo '<br>Error: <strong>' . $exception->getMessage(), '</strong><br><br>';

        echo 'Trace:', '<br><br><pre>';

        var_dump( $exception->getTrace() );

        echo '</pre>';

    }

}
