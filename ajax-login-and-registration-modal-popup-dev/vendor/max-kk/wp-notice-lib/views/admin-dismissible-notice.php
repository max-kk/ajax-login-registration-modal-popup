<?php 
/** @var array $notice */
?>
<div class="notice notice-<?php esc_attr_e( $notice['type'] ); ?> is-dismissible wp-is-dismissible" data-dismiss-url="<?php echo esc_attr($this->get_dismiss_url( $notice )); ?>">
	<p><?php echo wp_kses( $notice['message'], wp_kses_allowed_html( 'post' ) ); ?></p>
</div>
