<?php
/**
 * Textarea field class
 */

class LRM_Field_Roles {

    public static function _corrected_value( $value = null ) {
        $default = [
            'label' => [ 0=>'Subscriber' ],
            'roles' => [ [0=>'subscriber'] ],
        ];

        if ( $value ) {
            $default = array_merge($default, $value);
        }

        return $default;
    }

	/**
	 * Text field
	 * @param  underDEV\Utils\Settings\Field $field Field instance
	 * @return void
	 */
	public function input( $field ) {

        $value = self::_corrected_value( $field->value() );

        //var_dump($value['roles']);

        echo '<div class="lrm-repeaters-field lrm-repeaters-user_role">';

            $this->_add_new_tpl($field);
            echo '<hr>';

            echo '<div class="lrm-repeater-field__roles-wrap">';
                foreach ($value['label'] as $key => $name) {

                    echo '<div class="lrm-repeater-field__row" data-key="' , $key ,'">';
                        echo '<span class="lrm-repeater-field__row_actions">
                                <a href="#0" class="js-lrm-delete-row"><span class="dashicons dashicons-no lrm-repeater-field__row_action"></span></a>                                   
                            </span>';
                        $this->_roles_tpl($field, $key, $value);

                        echo '<a href="#0" class="js-lrm-sort-row" title="Drag to reorder"><span class="dashicons dashicons-menu"></span></a><hr>';
                    echo '</div>';
                }
            echo '</div>';

        echo '</div>';


        echo '<script type="text/html" class="js-lrm-repeater-tpl" data-name="', $field->input_name(), '">';
            echo '<div class="lrm-repeater-field__row" data-key="%key%">';
                echo '<span class="lrm-repeater-field__row_actions">
                                    <a href="#0" class="js-lrm-delete-row"><span class="dashicons dashicons-no lrm-repeater-field__row_action"></span></a>
                                </span>';

                $this->_roles_tpl($field, '%key%');
                echo '<a href="#0" class="js-lrm-sort-row" title="Drag to reorder"><span class="dashicons dashicons-menu"></span></a>';
            echo '<hr>';
            echo '</div>';
        echo '</script>';



        //echo '<textarea rows="' . $rows . '" id="' . $field->input_id() . '" name="' . $field->input_name() . '" class="large-text">' . stripslashes($field->value()) . '</textarea>';

	}

	public function _add_new_tpl( $field ) {
	    echo '<button type="button" class="js-lrm-add-new-redirect-rule button button-primary" data-name="', $field->input_name() , '">Add new role</button>';
    }
	public function _roles_tpl( $field, $key = '', $value = null ) {

	    $selected_roles = [];
        if ( '' !== $key && $value ) {
            $selected_roles = isset($value['roles'][$key]) ? $value['roles'][$key] : $selected_roles;
        }

        echo '<span class="lrm-repeater-field__roles">';
        echo 'Label ';

        echo '<input name="' . $field->input_name() . '[label][' . $key . ']" class="role-label" value="' , esc_attr($value['label'][$key]), '" data-lpignore="true">';

		echo ' assign role(s) ';

        echo '<select multiple rows=2 name="' . $field->input_name() . '[roles][' . $key . '][]" class="pretty-select">';

        foreach (LRM_Roles_Manager::get_wp_roles_flat() as $role_key => $role_name) {
            printf('<option value="%s" %s>%s</option>', $role_key, selected(!in_array($role_key, $selected_roles), false), $role_name);
        }
        echo '</select>';

        echo '<br/>';

        echo '</span>';

    }

	/**
	 * Sanitize input value
	 * @param  string $value Saved value
	 * @return string        Sanitized text
	 */
	public function sanitize( $value ) {

		return $value;

	}

}