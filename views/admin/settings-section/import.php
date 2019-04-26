<p>
	<?php
	foreach (LRM_Settings::get()->get_sections() as $section_key => $section) :
		if ( ! $section->export() ) {
			continue;
		}
		printf('<label><input type="checkbox" class="lrm_import_sections_checkbox" name="lrm_import_sections[]" value="%1$s" checked="checked"> %2$s</label><br/>', $section_key, $section->name());
	endforeach;
	?>
</p>
<p>
    <strong>Import string:</strong>
    <textarea rows="6" id="lrm-import-string" class="large-text "></textarea>
</p>
<p>
    <button type="button" class="button button-primary lrm-run-import" data-nonce="<?= wp_create_nonce('lrm_run_import'); ?>">Run import for the selected sections</button>
    <small>Only if this sections exists in a Imported string</small>
</p>
<style>
    .submit {
        display: none;
    }
</style>