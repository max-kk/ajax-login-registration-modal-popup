<?php
/**
 * Textarea field class
 */

class LRM_Field_Redirects {

    public static function _corrected_value( $value = null ) {
        $default = [
            'role_match' => [],
            'roles' => [],
            'redirect' => ['default'=>'url'],
            'redirect_url' => ['default'=>''],
            'redirect_page' => ['default'=>''],
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

        $per_role = ( null === $field->addon( 'per_role' ) ? true : $field->addon( 'per_role' ) );

        $value = self::_corrected_value( $field->value() );

        //var_dump($value['roles']);

        echo '<div class="lrm-repeaters-field">';
            if ( $per_role ) {
                $this->_add_new_tpl($field);
                echo '<hr>';

                echo '<div class="lrm-repeater-field__roles-wrap lrm-redirects-field__roles-wrap">';
                    foreach ($value['redirect'] as $key => $redirect) {

                        if ('default' === $key) {
                            continue;
                        }
                        echo '<div class="lrm-redirects-field__row lrm-repeater-field__row" data-key="' , $key ,'">';
                            echo '<span class="lrm-repeater-field__row_actions">
                                    <a href="#0" class="js-lrm-delete-row"><span class="dashicons dashicons-no lrm-redirects-field__row_action"></span></a>                                   
                                </span>';
                            $this->_roles_tpl($field, $key, $value);

                            $this->_redirects_tpl($field, $key, $value);
                            echo '<a href="#0" class="js-lrm-sort-row" title="Drag to reorder"><span class="dashicons dashicons-menu"></span></a><hr>';
                        echo '</div>';
                    }
                echo '</div>';


                echo '<span>If nothing match, </span>';
            }
            $this->_redirects_tpl( $field, 'default', $value );

        echo '</div>';

        if ( $per_role ) {
            echo '<script type="text/html" class="js-lrm-repeater-tpl" data-name="', $field->input_name(), '">';
                echo '<div class="lrm-redirects-field__row lrm-repeater-field__row" data-key="%key%">';
                    echo '<span class="lrm-repeater-field__row_actions">
                                        <a href="#0" class="js-lrm-delete-row"><span class="dashicons dashicons-no lrm-redirects-field__row_action"></span></a>
                                    </span>';

                    if ($per_role) {
                        $this->_roles_tpl($field, '%key%');
                        echo '<a href="#0" class="js-lrm-sort-row" title="Drag to reorder"><span class="dashicons dashicons-menu"></span></a>';
                    }
                    $this->_redirects_tpl($field, '%key%');
            echo '<hr>';
                echo '</div>';
            echo '</script>';
        }


        //echo '<textarea rows="' . $rows . '" id="' . $field->input_id() . '" name="' . $field->input_name() . '" class="large-text">' . stripslashes($field->value()) . '</textarea>';

	}

	public function _add_new_tpl( $field ) {
	    echo '<button type="button" class="js-lrm-add-new-redirect-rule button button-primary" data-name="', $field->input_name() , '">Add new rule</button>';
    }
	public function _roles_tpl( $field, $key = '', $value = null ) {

	    $selected_roles = [];
	    $role_match = 'any_of';
        if ( '' !== $key && $value ) {
            $selected_roles = isset($value['roles'][$key]) ? $value['roles'][$key] : $selected_roles;
            $role_match = isset($value['role_match'][$key]) ? $value['role_match'][$key] : $role_match;
        }

        echo '<span class="lrm-redirects-field__roles">';
        echo 'If role match ';

        echo '<select name="' . $field->input_name() . '[role_match][' . $key . ']" class="role-match">';
            printf('<option value="%s" %s>%s</option>', 'any_of', selected('any_of', $role_match), 'any of');
            printf('<option value="%s" %s>%s</option>', 'all', selected('all', $role_match), 'all');
        echo '</select>';


        echo '<select multiple rows=2 name="' . $field->input_name() . '[roles][' . $key . '][]" class="pretty-select">';
        foreach (LRM_Roles_Manager::get_wp_roles_flat() as $role_key => $role_name) {
            printf('<option value="%s" %s>%s</option>', $role_key, selected(!in_array($role_key, $selected_roles), false), $role_name);
        }
        echo '</select>';

        echo '<br/>';

        echo '</span>';

    }

    public function _redirects_tpl( $field, $key = '', $value = null ) {

	    $redirect = 'url';
	    $redirect_url = '';
	    $redirect_page = '';
	    if ( '' !== $key && $value ) {
            $redirect = isset($value['redirect'][$key]) ? $value['redirect'][$key] : $redirect;
            $redirect_url = isset($value['redirect_url'][$key]) ? $value['redirect_url'][$key] : $redirect_url;
            $redirect_page = isset($value['redirect_page'][$key]) ? $value['redirect_page'][$key] : $redirect_page;
        }

        echo '<span class="lrm-redirects-field__redirect">';

        echo ' redirect to ';

        echo '<select name="' . $field->input_name() . '[redirect][' . $key . ']" class="redirect-to">';
            printf('<option value="%s" %s>%s</option>', 'url', selected('url', $redirect), 'Custom URL');
            printf('<option value="%s" %s>%s</option>', 'page', selected('page', $redirect), 'Page');
            printf('<option value="%s" %s>%s</option>', 'bp_profile', selected('bp_profile', $redirect), 'BuddyPress profile');
            printf('<option value="%s" %s>%s</option>', 'wc_account', selected('wc_account', $redirect), 'WooCommerce account');
        echo '</select>';


        echo '<input type="text" name="' . $field->input_name() . '[redirect_url][' . $key . ']" value="' . esc_attr($redirect_url) . '" class="redirect-url" placeholder="Enter url">';

        echo '<select name="' . $field->input_name() . '[redirect_page][' . $key . ']" class="redirect-page">';
            echo '<option value="">== Select a page == </option>';
            foreach (LRM_Pages_Manager::_get_pages_arr() as $page_ID => $page_title) {
                printf('<option value="%s" %s>%s</option>', $page_ID, selected($page_ID, $redirect_page), $page_title);
            }
        echo '</select>';

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


//array(4) {
//    ["roles"]=>
//  array(2) {
//        [1]=>
//    string(6) "editor"
//        [0]=>
//    string(8) "customer"
//  }
//  ["redirect"]=>
//  array(3) {
//        [0]=>
//    string(3) "url"
//        [1]=>
//    string(3) "url"
//        ["default"]=>
//    string(3) "url"
//  }
//  ["redirect_url"]=>
//  array(3) {
//        [0]=>
//    string(3) "/1/"
//        [1]=>
//    string(4) "/2/2"
//        ["default"]=>
//    string(5) "/def/"
//  }
//  ["redirect_page"]=>
//  array(3) {
//        [0]=>
//    string(0) ""
//        [1]=>
//    string(0) ""
//        ["default"]=>
//    string(0) ""
//  }
//}