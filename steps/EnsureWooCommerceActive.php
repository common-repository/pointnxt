<?php

class Pointnxt_EnsureWooCommerceActive implements Pointnxt_Automation
{
	public function getName()
	{
		return __('Check if the WooCommerce plugin is activated', 'pointnxt');
	}

	public function runStep()
	{
		if (is_plugin_active('woocommerce/woocommerce.php')) {
			return new PNXT_StepResult(true);
		}

		activate_plugin('woocommerce/woocommerce.php');
		delete_transient('_wc_activation_redirect');

		if (is_plugin_active('woocommerce/woocommerce.php')) {
			return new PNXT_StepResult(true);
		}

		return new PNXT_StepResult(false, __('We failed to activate WooCommerce Plugin. Please activate the plugin and try again.', 'pointnxt'));
	}
}
