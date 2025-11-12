<?php
/**
 * Pass field class
 */

class LRM_Field_Password {

	/**
	 * Text field
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

		echo '<label><input type="password" id="' . esc_attr($field->input_id()) . '" name="' . esc_attr($field->input_name()) . '" value="' . esc_attr( wp_unslash($field->value()) ) . '" class="widefat"></label>';

	}

	/**
	 * Sanitize input value
	 * @param  string $value Saved value
	 * @return string        Sanitized text
	 */
	public function sanitize( $value ) {

		return sanitize_text_field( $value );

	}

}
