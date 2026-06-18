<?php
if ((!defined('ABSPATH'))) {
	die;
}

/**
 * Class PagePulse
 * The file that defines the core plugin class
 *
 * @author Mahesh Thorat
 * @link https://maheshthorat.web.app
 * @version 0.1
 * @package PagePulse
 */
class PagePulse_Admin
{
	private $plugin_name = PagePulse_PLUGIN_IDENTIFIER;
	private $version = PagePulse_PLUGIN_VERSION;
	private $notice = "";


	/**
	 * Register the stylesheet file(s) for the dashboard area
	 */
	public function enqueue_backend_standalone()
	{
		wp_register_style($this->plugin_name . '-standalone', plugin_dir_url(__FILE__) . 'assets/styles/standalone.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name . '-standalone');
	}

	/**
	 * Update `Options` on form submit
	 */
	/**
	 * Update `Options` on form submit
	 */
	public function return_update_options()
	{
		if (
			isset($_POST['PagePulse-update-option']) &&
			$_POST['PagePulse-update-option'] == 'true' &&
			check_admin_referer('pwm-referer-form', 'pwm-referer-option')
		) {
			// Initialize the options array with default values
			$opts = array(
				'default_PagePulse' => 'off',
				'page_background_option' => '',
				'pageLoadAnimation' => '',
				'loading_icon_option' => '',
			);
			if (isset($_POST['default_PagePulse'])) {
				$opts['default_PagePulse'] = 'on';
			}
			if (isset($_POST['page_background_option'])) {
				$opts['page_background_option'] = 'on';
			}
			if (isset($_POST['pageLoadAnimation'])) {
				$opts['pageLoadAnimation'] = isset($_POST['pageLoadAnimation']) ? sanitize_text_field($_POST['pageLoadAnimation']) : '';
			}
			if (isset($_POST['loading_icon_option'])) {
				$opts['loading_icon_option'] = 'on';
			}
			if (isset($_POST['page_background_color'])) {
				$opts['page_background_color'] = isset($_POST['page_background_color']) ? sanitize_hex_color($_POST['page_background_color']) : '';
			}
			if (isset($_POST['page_background_opacity'])) {
				$opts['page_background_opacity'] = isset($_POST['page_background_opacity']) ? sanitize_text_field($_POST['page_background_opacity']) : '';
			}

			update_option('_PagePulse', $opts);

			$this->notice = array('success', __('Your settings have been successfully updated.', 'pagepulse'));

			// Redirect to the settings page with a success status
			// wp_safe_redirect(admin_url('options-general.php?page=PagePulse-admin') . '&status=updated');
			// exit;
		}
	}

	/**
	 * Return the `Options` page
	 */
	public function return_options_page()
	{
		$opts = get_option('_PagePulse');
		$nonce = wp_create_nonce('pagepulse');

		if (isset($_GET['con']) && $_GET['con'] == 'about' && wp_verify_nonce($nonce, 'pagepulse')) {
			$this->return_about_page();
		} else if (isset($_GET['con']) && $_GET['con'] == 'donate' && wp_verify_nonce($nonce, 'pagepulse')) {
			$this->return_donate_page();
		} else {
?>
			<div class="wrap">
				<section class="wpbnd-wrapper">
					<div class="wpbnd-container">
						<div class="wpbnd-tabs">
							<?php echo wp_kses_post($this->return_plugin_header('tab1')); ?>
							<main class="privacy-settings-header" style="padding: 0 15px; padding-bottom: 15px;text-align: left;">
								<section class="tab-section">
									<?php if (isset($this->notice) && !empty($this->notice)) { ?>
										<div class="updated">
											<p><?php echo esc_attr($this->notice[1], 'pagepulse'); ?> <a href="<?php echo esc_url(get_admin_url() . 'options-general.php?page=PagePulse-admin'); ?>">Hide Notice</a></p>
										</div>
									<?php } ?>
									<form method="POST" enctype="multipart/form-data">
										<input type="hidden" name="PagePulse-update-option" value="true" />
										<?php wp_nonce_field('pwm-referer-form', 'pwm-referer-option'); ?>
										<div class="wpbnd-form">
											<table class="form-table">
												<?php $fieldID = uniqid(); ?>
												<tr>
													<th>
														<h3>
															<span class="dashicons dashicons-yes-alt"></span>
															<?php esc_html_e('Turn on PagePulse', 'pagepulse'); ?>
														</h3>
													</th>
													<td>
														<label class="switchContainer">
															<input id="<?php echo esc_attr($fieldID); ?>" type="checkbox" name="default_PagePulse" class="default_PagePulse" <?php echo (isset($opts['default_PagePulse']) && $opts['default_PagePulse'] == 'on') ? 'checked="checked"' : ''; ?> />
															Turn On
														</label>
														<p class="description"><i>This option will allows animations in website</i></p>
													</td>
												</tr>
											</table>
											<table class="pluginOptions <?php echo (!isset($opts['default_PagePulse']) || $opts['default_PagePulse'] != 'on') ? 'disabledOption' : ''; ?>">
												<tr>
													<td>
														<table class="form-table">
															<?php $fieldID = uniqid(); ?>
															<tr>
																<th>
																	<h3>
																		<span class="dashicons dashicons-admin-site"></span>
																		<?php esc_html_e('Select Effect', 'pagepulse'); ?>
																	</h3>
																</th>
																<td>
																	<select id="<?php echo esc_attr($fieldID); ?>" name="pageLoadAnimation">
																		<option <?php (isset($opts['pageLoadAnimation'])) && selected($opts['pageLoadAnimation'], 'upDown'); ?> value="slideUpDown">Slide Top-Bottom</option>
																		<option <?php (isset($opts['pageLoadAnimation'])) && selected($opts['pageLoadAnimation'], 'upDown'); ?> value="slideDownUp">Slide Bottom-Top</option>
																		<option <?php (isset($opts['pageLoadAnimation'])) && selected($opts['pageLoadAnimation'], 'fadeInOut'); ?> value="fadeInOut">FadeIn/FadeOut</option>
																		<option <?php (isset($opts['pageLoadAnimation'])) && selected($opts['pageLoadAnimation'], 'clipFromCenter'); ?> value="clipFromCenter">Clip Top/Bottom from Center</option>
																		<option <?php (isset($opts['pageLoadAnimation'])) && selected($opts['pageLoadAnimation'], 'clipLeftRight'); ?> value="clipLeftRight">Clip Left/Right from Center</option>
																		<option <?php (isset($opts['pageLoadAnimation'])) && selected($opts['pageLoadAnimation'], 'animatedFullSlideTopBottom'); ?> value="animatedFullSlideTopBottom">Animated Slide Top to Bottom</option>
																		<option <?php (isset($opts['pageLoadAnimation'])) && selected($opts['pageLoadAnimation'], 'animatedFullSlideBottomTop'); ?> value="animatedFullSlideBottomTop">Animated Slide Bottom to Top</option>

																	</select>
																</td>
															</tr>
															<?php $fieldID = uniqid(); ?>

															<tr class="pageBackgroundWrap">
																<th>
																	<h3>
																		<span class="dashicons dashicons-admin-appearance"></span>
																		<?php esc_html_e('Background Color', 'pagepulse'); ?>
																	</h3>
																</th>
																<td>
																	<input type="color" name="page_background_color" id="page_background_color" value="<?php echo esc_attr((isset($opts['page_background_color'])) ? $opts['page_background_color'] : PagePulse_DEFAULT_COLOR); ?>">
																</td>
															</tr>
															<tr>
																<th>
																	<h3>
																		<span class="dashicons dashicons-feedback"></span>
																		<?php esc_html_e('Opacity', 'pagepulse'); ?>
																	</h3>
																</th>
																<td>
																	<input type="range" min="0.1" max="1" step="0.01" name="page_background_opacity" id="page_background_opacity" value="<?php echo esc_attr((isset($opts['page_background_opacity'])) ? $opts['page_background_opacity'] : 1); ?>">
																	<b class="opacityVal"></b>
																</td>
															</tr>
														</table>
													</td>
													<td>
														<h3><?php echo esc_attr('<Preview/>', 'pagepulse'); ?></h3>
														<div class="desktop-container">
															<div class="loadingAnimationPreview"></div>
															<div style="padding: 10px;">
																<h2>Demo Title</h2>
																<h4>Sample Content</h4>
																<p>Sample paragraph contentSample paragraph contentSample paragraph contentSample paragraph contentSample paragraph contentSample paragraph contentSample paragraph contentSample paragraph content</p>
															</div>
														</div>
													</td>
												</tr>
											</table>
											<div class="form-footer">
												<br>
												<input type="submit" class="button button-primary button-theme" value="<?php esc_html_e('Update Settings', 'pagepulse'); ?>">
											</div>
										</div>
									</form>
								</section>
							</main>
						</div>
					</div>
				</section>
			</div>
		<?php
		}
	}

	/**
	 * Return the plugin header
	 */
	public function return_plugin_header($tab)
	{
		$link = admin_url('options-general.php');
		$list = array(
			array('tab1', 'PagePulse-admin', 'fa-cogs', __('<span class="dashicons dashicons-admin-tools"></span> Settings', 'pagepulse')),
			array('tab2', 'PagePulse-admin&con=about', 'fa-info-circle', __('<span class="dashicons dashicons-editor-help"></span> About', 'pagepulse')),
			// array('tab3', 'PagePulse-admin&con=donate', 'fa-info-circle', __('<span class="dashicons dashicons-money-alt"></span> Donate', 'pagepulse'))
		);

		$menu = null;
		foreach ($list as $item => $value) {
			$menu .= '<a class="privacy-settings-tab ' . $value[0] . ' ' . (($tab == $value[0]) ? 'active' : '') . '" href="' . $link . '?page=' . $value[1] . '">' . $value[3] . '</a>';
		}

		$html = '
			<h2></h2>
			<div class="privacy-settings-header">
				<div class="privacy-settings-title-section">
						<h1>PagePulse</h1>
				</div>
				<nav class="privacy-settings-tabs-wrapper hide-if-no-js" aria-label="Secondary menu">
						' . $menu . '
				</nav>
			</div>
		';

		return $html;
	}

	/**
	 * Return the `About` page
	 */
	public function return_about_page()
	{
		?>
		<div class="wrap">
			<section class="wpbnd-wrapper">
				<div class="wpbnd-container">
					<div class="wpbnd-tabs">
						<?php echo wp_kses_post($this->return_plugin_header('tab2')); ?>
						<main class="privacy-settings-header">
							<section class="tab-section">
								<br>
								<img alt="Mahesh Thorat" src="https://secure.gravatar.com/avatar/13ac2a68e7fba0cc0751857eaac3e0bf?s=100&amp;d=mm&amp;r=g" srcset="https://secure.gravatar.com/avatar/13ac2a68e7fba0cc0751857eaac3e0bf?s=200&amp;d=mm&amp;r=g 2x" class="avatar avatar-100 photo profile-image" height="100" width="100">

								<div class="profile-by">
									<p>© <?php echo esc_attr(gmdate('Y')); ?> - created by <a class="link" href="https://maheshthorat.web.app/" target="_blank"><b>Mahesh Mohan Thorat</b></a></p>
								</div>
							</section>
							<section class="helpful-links">
								<b>helpful links</b>
								<ul>
									<li><a href="https://pagespeed.web.dev/" target="_blank">PageSpeed</a> | <a href="https://gtmetrix.com/" target="_blank">GTmetrix</a> | <a href="https://www.webpagetest.org" target="_blank">Web Page Test</a> | <a href="https://http3check.net/" target="_blank">http3check</a> | <a href="https://sitecheck.sucuri.net/" target="_blank">Sucuri - security check</a></li>
								</ul>
							</section>
						</main>
					</div>
				</div>
			</section>
		</div>
	<?php
	}

	public function return_donate_page()
	{
	?>
		<div class="wrap">
			<section class="wpbnd-wrapper">
				<div class="wpbnd-container">
					<div class="wpbnd-tabs">
						<?php echo wp_kses_post($this->return_plugin_header('tab3')); ?>
						<main class="tabs-main about">
							<section class="">
								<table class="wp-list-table widefat fixed striped table-view-list">
									<tbody id="the-list">
										<tr>
											<td><a href="https://buymeacoffee.com/maheshmthorat" target="_blank"><img width="160" src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__))); ?>admin/assets/img/razorpay.svg" /></a></td>
										</tr>
										<tr>
											<td>
												<h3>Scan below code</h3>
												<img width="350" src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__))); ?>admin/assets/img/qr.svg" />
												<br>
												<img width="350" src="<?php echo esc_url(plugin_dir_url(dirname(__FILE__))); ?>admin/assets/img/upi.png" />
												<br>
												<b>Mr Mahesh Mohan Thorat</b>
												<h3>UPI - maheshmthorat@oksbi</h3>
											</td>
										</tr>
									</tbody>
								</table>
							</section>
							<section class="helpful-links">
								<b>helpful links</b>
								<ul>
									<li><a href="https://pagespeed.web.dev/" target="_blank">PageSpeed</a></li>
									<li><a href="https://gtmetrix.com/" target="_blank">GTmetrix</a></li>
									<li><a href="https://www.webpagetest.org" target="_blank">Web Page Test</a></li>
									<li><a href="https://http3check.net/" target="_blank">http3check</a></li>
									<li><a href="https://sitecheck.sucuri.net/" target="_blank">Sucuri - security check</a></li>
								</ul>
							</section>
						</main>
					</div>
				</div>
			</section>
		</div>
<?php	}

	/**
	 * Return Backend Menu
	 */
	public function return_admin_menu()
	{
		add_options_page(PagePulse_PLUGIN_FULLNAME, PagePulse_PLUGIN_FULLNAME, 'manage_options', 'PagePulse-admin', array($this, 'return_options_page'));
	}

	public function PagePulse_settings_link($links)
	{
		$url = get_admin_url() . 'options-general.php?page=PagePulse-admin';
		$settings_link = ["<a href='$url'>" . __('Settings') . '</a>', "<a href='https://buymeacoffee.com/maheshmthorat' target='_blank'>Say Thanks</a>"];
		$links = array_merge(
			$settings_link,
			$links
		);
		return $links;
	}

	public static function staticImages($img)
	{
		$imgReturn = '';
		switch ($img) {
			case "animatedCheckBox":
				$imgReturn = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" width="20px" height="20px">
  <!-- Rotating Circles -->
  <circle cx="50" cy="50" r="45" fill="none" stroke="#0073e6" stroke-width="5">
    <animate attributeName="r" repeatCount="indefinite" dur="2s" values="20;45;20" keyTimes="0;0.5;1" begin="0s"></animate>
  </circle>
  <circle cx="50" cy="50" r="20" fill="none" stroke="#ff5722" stroke-width="5">
    <animate attributeName="r" repeatCount="indefinite" dur="2s" values="45;20;45" keyTimes="0;0.5;1" begin="-1s"></animate>
  </circle>
  <circle cx="50" cy="50" r="20" fill="none" stroke="#ffc107" stroke-width="5">
    <animate attributeName="r" repeatCount="indefinite" dur="2s" values="20;45;20" keyTimes="0;0.5;1" begin="-0.5s"></animate>
  </circle>
</svg>
';
				break;
			default:
				break;
		}
		return $imgReturn;
	}
}

?>