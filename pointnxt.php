<?php
/*
Plugin Name: PointNXT
Description: Helps you easily integrate your WooCommerce store with PointNXT.
Version: 1.0.1
Author: AdaptNXT
Author URI: https://adaptnxt.com
License: GPL2
Text Domain: pointnxt
*/

class PointnxtPlugin
{
	/** @var Pointnxt_Automation[] */
	public $steps = [];

	public function registerPluginHooks()
	{
		add_menu_page('Pointnxt Integration', 'PointNXT', 'manage_options', 'pointnxt', [$this, 'pnxt_renderPage']);
		add_action('admin_action_pointnxt_integrate', [$this, 'pnxt_integrate']);
		add_action('admin_enqueue_scripts', [$this, 'pnxt_enqueueScripts']);
	}

	function pnxt_integrate()
	{
		$stepIndex = isset($_POST['step']) ? intval($_POST['step']) : -1;
		$result    = $this->runStep($stepIndex);

		echo json_encode($result);
		exit();
	}

	/**
	 * @param int $stepIndex
	 *
	 * @return PNXT_StepResult
	 */
	function runStep($stepIndex)
	{
		if ($stepIndex < 0 || $stepIndex >= count($this->steps)) {
			return new PNXT_StepResult(
				false,
				__('Invalid integration step received. Please contact our support.', 'pointnxt')
			);
		}

		return $this->steps[$stepIndex]->runStep();
	}

	function pnxt_enqueueScripts()
	{
		wp_enqueue_script(
			'pointnxt-js',
			plugin_dir_url(__FILE__).'js/pointnxt.js',
			array('jquery'),
			'0.2'
		);

		wp_enqueue_style(
			'pointnxt-css',
			plugin_dir_url(__FILE__).'css/styles.css',
			array(),
			'0.2'
		);
	}

	function pnxt_renderPage()
	{
		echo '<h1><img src="' . json_encode(home_url()) . 'images/pointnxtlogo.png"> Integration</h1>';

		if (!empty(get_option('woocommerce_pointnxt_consumer_key'))) {
			$buttonLabel = __('Re-connect to Pointnxt', 'pointnxt');
		} else {
			$buttonLabel = __('Connect to Pointnxt', 'pointnxt');
		}

		?>
		<script>
			var pointnxtBaseUrl = <?php echo json_encode(admin_url('admin.php')); ?>;
			var pointnxtStoreUrl = <?php echo json_encode(home_url()); ?>;
			var integrationStepCount = <?php echo json_encode(count($this->steps)); ?>;
			var defaultIntegrationError = <?php echo json_encode(__('Could not connect to the website to complete the integration step. Please, try again.', 'pointnxt')) ?>;
			var successfulIntegrationMessage = <?php echo json_encode(__('Successfully prepared to integrate with Pointnxt!', 'pointnxt')) ?>;
		</script>
		<div id="pointnxt-description">
			<p>Easily activate Pointnxt Integration with WooCommerce. Connect Pointnxt and WooCommerce on your website
				with a single click of the button below.</p>
			<p>By clicking the button below, you are acknowledging that Pointnxt can make the following changes:</p>
			<ul style="list-style: circle inside;">
				<?php foreach ($this->steps as $index => $step) { ?>
					<li><?php echo esc_html($step->getName()); ?></li>
				<?php } ?>
			</ul>
			<form method="post" action="<?php echo esc_url(admin_url('admin.php')); ?>" novalidate="novalidate">
				<p class="submit">
					<input type="hidden" name="action" value="pointnxt_integrate"/>
					<input type="hidden" name="step" value="0"/>
					<input type="submit" value="<?php echo esc_attr($buttonLabel); ?>" class="button button-primary" id="btn-submit">
				</p>
			</form>
		</div>
		<div id="pointnxt-progress" style="display: none">
			Integration progress:
			<ol>
				<?php foreach ($this->steps as $index => $step) { ?>
					<li id="pointnxt-step-<?php echo esc_attr($index); ?>">
						<?php echo esc_html($step->getName()); ?>
					</li>
				<?php } ?>
			</ol>
			<p id="pointnxt-result">
			</p>
		</div>
        <?php  if(@$_GET['reconnect'] == 1) {?>
            <script>
                var link = document.getElementById('btn-submit');
                link.click()
            </script>
        <?php } ?>
		<?php
	}
}

include_once('PNXT_StepResult.php');
include_once('steps/Pointnxt_Automation.php');
include_once('steps/EnsureWooCommercePlugin.php');
include_once('steps/EnsureWooCommerceActive.php');
include_once('steps/EnableWooCommerceAPI.php');
include_once('steps/GenerateWooCommerceKeys.php');
include_once('steps/SendWooCommerceKeys.php');

$pointnxtPlugin          = new PointnxtPlugin();
$pointnxtPlugin->steps[] = new Pointnxt_EnsureWooCommercePlugin();
$pointnxtPlugin->steps[] = new Pointnxt_EnsureWooCommerceActive();
$pointnxtPlugin->steps[] = new Pointnxt_EnableWooCommerceAPI();
$pointnxtPlugin->steps[] = new Pointnxt_GenerateWooCommerceKeys();
$pointnxtPlugin->steps[] = new Pointnxt_SendWooCommerceKeysStep();

add_action('admin_menu', [$pointnxtPlugin, 'registerPluginHooks']);