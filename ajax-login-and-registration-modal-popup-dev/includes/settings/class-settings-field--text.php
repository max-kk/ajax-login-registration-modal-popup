<?php
/**
 * Text field class
 */

class LRM_Field_Text {

	/**
	 * Text field
	 * @param  Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

		echo '<label><input type="text" id="' . $field->input_id() . '" name="' . $field->input_name() . '" value="' . esc_attr( stripslashes($field->value()) ) . '" class="widefat"></label>';

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
