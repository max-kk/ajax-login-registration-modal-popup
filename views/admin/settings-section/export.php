<p>
	<?php
	foreach (LRM_Settings::get()->get_sections() as $section_key => $section) :
		if ( ! $section->export() ) {
			continue;
		}
		printf('<label><input type="checkbox" class="lrm_export_sections_checkbox" name="lrm_export_sections[]" value="%1$s" checked="checked"> %2$s</label><br/>', $section_key, $section->name());
	endforeach;
	?>
</p>
<p class="lrm-export-string-wrap" style="display: none;">
    <strong>Your exported settings string:</strong>
    <textarea rows="6" id="lrm-export-string" class="large-text "></textarea>
</p>

<p>
    <button type="button" class="button button-primary lrm-run-export" data-nonce="<?= wp_create_nonce('lrm_run_export'); ?>">Run export for the selected sections</button>
</p>
<style>
    .submit {
        display: none;
    }
</style>