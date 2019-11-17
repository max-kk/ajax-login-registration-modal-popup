<?php
$icons_selected = lrm_setting('skins/skin/icons');
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
    font-family: '<?= $font_name; ?>'; font-weight: normal; font-style: normal;
    src: url('<?= $font_path; ?>.eot?i8nbsv');
    src: url('<?= $font_path; ?>.eot?i8nbsv#iefix') format('embedded-opentype'),
    url('<?= $font_path; ?>.ttf?i8nbsv') format('truetype'),
    url('<?= $font_path; ?>.woff?i8nbsv') format('woff'),
    url('<?= $font_path; ?>.svg?i8nbsv#<?= $font_name; ?>') format('svg');
}
[class^="lrm-ficon-"]:before, [class*=" lrm-ficon-"]:before { font-family: '<?= $font_name; ?>' !important; }
<?php endif; ?>
</style>
