<?php
/**
 * Textarea field class
 */

class LRM_Field_Textarea_With_Html_Extended {

	/**
	 * Text field
	 * @param  underDEV\Utils\Settings\Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

        $rows = (int)$field->addon( 'rows' ) ? $field->addon( 'rows' ) : 3;

		echo '<textarea rows="' . $rows . '" id="' . $field->input_id() . '" name="' . $field->input_name() . '" class="large-text">' . stripslashes($field->value()) . '</textarea>';

	}

	/**
	 * Sanitize input value
	 * @param  string $value Saved value
	 * @return string        Sanitized text
	 */
	public function sanitize( $value ) {

		return balanceTags($value);

	}

}
