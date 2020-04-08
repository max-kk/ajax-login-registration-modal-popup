<div class="wrap underdev-settings <?php echo $this->handle; ?>-settings">

	<h1><?php _e( 'Settings', 'lrm' ) ?></h1>

	<?php if ( empty( $sections ) ): ?>
		<p><?php _e( 'No Settings available at the moment', 'lrm' ); ?></p>
	<?php else: ?>

		<div class="menu-col box">

			<ul class="menu-list">

				<?php foreach ( $this->get_sections() as $section_slug => $section ): ?>

					<?php
					$class = ( $section_slug == $current_section ) ? 'current' : '';
					if ( $section_slug !== 'license' ) {
                        $page_url = remove_query_arg('updated');
                        $url = add_query_arg('section', $section_slug, $page_url);
                    } else {
					    // MAX
                        // @since 2.01
                        $url = admin_url('options-general.php?page=lrm_api_manager_dashboard');
                    }
					?>

					<li class="<?php echo esc_attr( $class ); ?>"><a href="<?php echo esc_attr( $url ); ?>"><?php echo $section->name() ?></a></li>

				<?php endforeach ?>

			</ul>

		</div>

		<div class="setting-col box">

			<?php $section = $this->get_section( $current_section ); ?>

			<?php do_action( $this->handle . '/settings/section/' . $section->slug() . '/before' ); ?>

			<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" enctype="multipart/form-data">

				<?php wp_nonce_field( 'save_' . $this->handle . '_settings', 'nonce' ); ?>

				<input type="hidden" name="action" value="save_<?php echo $this->handle; ?>_settings">

				<?php
				/**
				 * When you have only checkboxed in the section, no data in POST is returned. This fields adds a dummy value
				 * for form handler so it could grab the section name and parse all defined fields
				 */
				?>
				<input type="hidden" name="<?php echo $this->handle . '_settings[' . $section->slug() . ']' ?>" value="section_buster">

				<?php $groups  = $section->get_groups(); ?>

				<?php foreach ( $groups as $group ): ?>

					<div class="setting-group">

						<h3>
							<?php
							echo esc_html( $group->name() );
							if ( LRM_Polylang_Integration::is_active() ) {
								if ( $group->can_be_translated() ) {
									echo ' <code>MultiLanguage text <a href="https://docs.maxim-kaminsky.com/lrm/kb/multi-language-set-up-via-polylang/#multilanguage-text-translation" target="_blank"><span class="dashicons dashicons-info"></span></a></code>';
								} elseif ( $group->only_base_language() ) {
									echo ' <code>No MultiLanguage <a href="https://docs.maxim-kaminsky.com/lrm/kb/multi-language-set-up-via-polylang/#no-multilanguage-group" target="_blank"><span class="dashicons dashicons-info"></span></a> - can be changed only on the base language "' . pll_default_language('name') . '"</code>';
								}
							} elseif ( LRM_WPML_Integration::is_wpml_active() ) {
								if ( $group->can_be_translated() ) {
									echo ' <code>MultiLanguage text <a href="https://docs.maxim-kaminsky.com/lrm/kb/multi-language-support-via-wpml/" target="_blank"><span class="dashicons dashicons-info"></span></a></code>';
								}
							}

							?>
						</h3>

						<?php $description = $group->description(); ?>

						<?php if ( ! empty( $description ) ): ?>
							<p class="description"><?php echo $description; ?></p>
						<?php endif ?>

						<?php do_action( $this->handle . '/settings/group/' . $group->slug() . '/before' ); ?>

						<table class="form-table">

							<?php foreach ( $group->get_fields() as $field ): ?>

								<tr class="tr-<?php echo esc_attr( $field->input_id() ); ?>">
									<th><label for="<?php echo esc_attr( $field->input_id() ); ?>"><?php echo esc_html( $field->name() ); ?></label></th>
									<td>
										<?php
										$field->render();
										$field_description = $field->description();
										?>
										<?php if ( ! empty( $field_description ) ): ?>
											<p><?php echo wp_kses_post($field_description); // MAX ?></p>
										<?php endif ?>
									</td>
								</tr>

							<?php endforeach ?>

						</table>

						<?php do_action( $this->handle . '/settings/sections/after', $group->slug() ); ?>

					</div>

				<?php endforeach ?>

				<?php if ( ! empty( $groups ) ): ?>
					<?php submit_button(); ?>
				<?php endif ?>

			</form>

			<?php do_action( $this->handle . '/settings/section/' . $section->slug() . '/after' ); ?>

		</div>

	<?php endif ?>

</div>
