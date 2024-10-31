<?php

class Pointnxt_EnableWooCommerceAPI implements Pointnxt_Automation
{
	public function getName()
	{
		return __('Enable WooCommerce REST API', 'pointnxt');
	}

	public function runStep()
	{
		update_option('woocommerce_api_enabled', 'yes');
		return new PNXT_StepResult(true);
	}
}
