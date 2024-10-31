<?php

class Pointnxt_EnsureWooCommercePlugin implements Pointnxt_Automation
{
	public function getName()
	{
		return __('Check if a WooCommerce version is supported', 'pointnxt');
	}

	public function runStep()
	{
		$version = $this->ensureWooCommercePlugin();

		if ($version === false) {
			return new PNXT_StepResult(false, __('WooCommerce currently not installed. Please install and activate WooCommerce and try again.', 'pointnxt'));
		}

		return new PNXT_StepResult(true);
	}

	function ensureWooCommercePlugin()
	{
		$pluginInstalled = $this->isPluginInstalled('woocommerce/woocommerce.php');

		if ($pluginInstalled !== false) {
			return $pluginInstalled['Version'];
		}
		return false;
	}

	function isPluginInstalled($slug)
	{
		if (!function_exists('get_plugins')) {
			require_once ABSPATH.'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		if (!empty($all_plugins[$slug])) {
			return $all_plugins[$slug];
		} else {
			return false;
		}
	}
}
