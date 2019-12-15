<ul class="lrm-switcher <?= ! $users_can_register ? '-is-login-only' : '-is-not-login-only'; ?>">

	<li><a href="#0" class="lrm-switch-to-link lrm-switch-to--login lrm-ficon-login <?php echo !$users_can_register || $is_inline && $default_tab == 'login' ? 'selected' : ''; ?>">
			<?php echo lrm_setting('messages/login/heading', true); ?>
		</a></li>

	<?php if ( $users_can_register ): ?>
		<li><a href="#0" class="lrm-switch-to-link lrm-switch-to--register lrm-ficon-register <?php echo $default_tab == 'register' ? 'selected' : ''; ?>">
				<?php echo lrm_setting('messages/registration/heading', true); ?>
			</a></li>
	<?php endif; ?>
</ul>