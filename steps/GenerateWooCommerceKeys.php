<?php

class Pointnxt_GenerateWooCommerceKeys implements Pointnxt_Automation
{
	public function getName()
	{
		return __('Create WooCommerce API keys for the Pointnxt admin user', 'pointnxt');
	}

	public function runStep()
	{
		pointnxt_WC_Auth();

		if (!class_exists('Pointnxt_WC_Auth')) {
			return new PNXT_StepResult(false, 'Could not find WooCommerce plugin. Please try again.');
		}

		$user = wp_get_current_user();

		if (!$user) {
			return new PNXT_StepResult(false, 'Pointnxt Administrator user not found. Please try again.');
		}

		$apiKey = (new Pointnxt_WC_Auth())->createAPIKey($user->ID);

		// store the key and secret
		if (!empty($apiKey['consumer_key'])) {
			update_option('woocommerce_pointnxt_consumer_key', $apiKey['consumer_key']);
		}
		if (!empty($apiKey['consumer_secret'])) {
			update_option('woocommerce_pointnxt_consumer_secret', $apiKey['consumer_secret']);
		}

		return new PNXT_StepResult(true, null, $apiKey);
	}
}

function pointnxt_WC_Auth()
{
	if (class_exists('WC_Auth')) {
		class Pointnxt_WC_Auth extends WC_Auth
		{
			public function createAPIKey($userId)
			{
				return $this->create_keys(
					'Pointnxt Integration',
					$userId,
					'read_write'
				);
			}
		}
	}
}
