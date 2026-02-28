<?php
$icons_selected = lrm_setting('skins/skin/icons');
$icons_allowed  = array( 'svg', 'icomoon', 'material', 'fa4', 'fa5-free' );

if ( ! in_array( $icons_selected, $icons_allowed, true ) ) {
    $icons_selected = 'svg';
}
?>
<style>/* LRM */
body.logged-in .lrm-hide-if-logged-in { display: none !important; }
body.logged-in [class*='lrm-hide-if-logged-in'] { display: none !important; }
body:not(.logged-in) .lrm-show-if-logged-in { display: none !important; }
body:not(.logged-in) [class*='lrm-show-if-logged-in'] { display: none !important; }
<?php if ( 'svg' !== $icons_selected ) :
$icons_map = [
    'icomoon'   => 'ico-icomoon',
    'material'  => 'ma-icomoon',
    'fa4'       => 'fa4-icomoon',
    'fa5-free'  => 'fa5-free',
];
$font_name = 'lrm-' . $icons_selected . '-icomoon';
$font_path =  LRM_URL . 'assets/lrm-' . $icons_selected . '/fonts/lrm-' . $icons_map[$icons_selected];
?>
@font-face {
    font-family: '<?php echo esc_attr($font_name); ?>'; font-weight: normal; font-style: normal;
    src: url('<?php echo esc_url($font_path); ?>.eot?i8nbsv');
    src: url('<?php echo esc_url($font_path); ?>.eot?i8nbsv#iefix') format('embedded-opentype'),
    url('<?php echo esc_url($font_path); ?>.ttf?i8nbsv') format('truetype'),
    url('<?php echo esc_url($font_path); ?>.woff?i8nbsv') format('woff'),
    url('<?php echo esc_url($font_path); ?>.svg?i8nbsv#<?php echo esc_attr($font_name); ?>') format('svg');
}
[class^="lrm-ficon-"]:before, [class*=" lrm-ficon-"]:before { font-family: '<?php echo esc_html($font_name); ?>' !important; }
<?php endif; ?>
</style>
