<?php

class Pointnxt_SendWooCommerceKeysStep implements Pointnxt_Automation
{
	const URL_APP = 'https://app.pointnxt.com';
	public function getName()
	{
		return __('Send WooCommerce API keys to Pointnxt', 'pointnxt');
	}

	public function runStep()
	{
		$consumerKey    = get_option('woocommerce_pointnxt_consumer_key');
		$consumerSecret = get_option('woocommerce_pointnxt_consumer_secret');

		if (empty($consumerKey) || empty($consumerSecret)) {
			return new PNXT_StepResult(false, 'Could not find WooCommerce API key. Please try again.');
		}

		return new PNXT_StepResult(true, 'Redirecting to Pointnxt...', ['consumer_key' => $consumerKey, 'consumer_secret' => $consumerSecret, 'url' => $this->getRedirectUrl($consumerKey, $consumerSecret)]);
	}

	function getRedirectUrl( $consumerKey, $consumerSecret ) {

		$url     = self::URL_APP . '/setup/woocommerce?consumer_key=' . $consumerKey;
		$url     .= '&consumer_secret=' . $consumerSecret;
		$url     .= '&channel_url=' . urlencode( site_url() );
		if(@$_GET['reconnect'] == 1){
			$url .= '&reconnect=1';
		}
		return $url;
	}
}
