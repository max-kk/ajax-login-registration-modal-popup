<?php foreach ( $this->notices[ $type ] as $notice ) : ?>
	<div class="bs-callout bs-callout-<?php esc_attr_e( $type ); ?>">
		<p><?php echo wp_kses( $notice, wp_kses_allowed_html( 'post' ) ); ?></p>
	</div>
<?php endforeach; ?>
