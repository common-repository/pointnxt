jQuery( document ).on( 'submit', '#pointnxt-description form', function ( e ) {
	e.preventDefault();
	jQuery( '#pointnxt-description' ).hide();
	jQuery( '#pointnxt-progress' ).show();

	pointnxtLaunchStep( 0 );
});

var currentStep;

function pointnxtFinishIntegration(result, isError, data ) {
	var $result = jQuery( '#pointnxt-result' );

	if ( isError ) {
		$result.html( result) ;
		$result.addClass( 'is-error' );
		return;
	}

	$result.removeClass( 'is-error' );
	$result.html( result + '<ul><li><b>Store URL:</b> ' + pointnxtStoreUrl + '</li><li><b>API Key:</b> ' + data[ 'consumer_key' ] + '</li><li><b>API Secret:</b> ' + data[ 'consumer_secret' ] + '</li><li><b>PointNXT Connect URL:</b> <a href="' + data[ 'url' ] + '" target="_blank">' + data[ 'url' ] + '</a></li></ul>' );

	// try to open the URL in a new tab
	var win = window.open( data[ 'url' ], '_blank' );
	if ( win ) {
		// browser has allowed it to be opened
		win.focus();
	} else {
		// browser has blocked it, open in the same tab
		window.location = data[ 'url' ];
	}
}

function pointnxtStepResponseHandler(response ) {
	var data = response ? JSON.parse( response ) : null;

	if ( !data || !data.success ) {
		jQuery( '#pointnxt-step-' + currentStep ).addClass( 'step-failed' );
		pointnxtFinishIntegration( !data || !data.message ? defaultIntegrationError : data.message, true );
		return;
	}

	if ( currentStep + 1 === integrationStepCount ) {
		++currentStep;
		pointnxtUpdateIntegrationProgress();
		pointnxtFinishIntegration( successfulIntegrationMessage, false, data.data );
		return;
	}

	pointnxtLaunchStep( currentStep + 1 );
}

function pointnxtLaunchStep(step ) {
	currentStep = step;
	pointnxtUpdateIntegrationProgress();

	jQuery.ajax( {
		type: "POST",
		url: pointnxtBaseUrl,
		data: {
			action: 'pointnxt_integrate',
			step: currentStep
		}
	} ).always( pointnxtStepResponseHandler );
}

function pointnxtUpdateIntegrationProgress() {
	for ( var i = 0; i < integrationStepCount; ++i ) {
		var $step = jQuery( '#pointnxt-step-' + i );
		$step.removeClass( 'step-in-progress' );
		$step.removeClass( 'step-complete' );
		$step.removeClass( 'step-failed' );

		if (i <= currentStep) {
			$step.addClass( ( i === currentStep ) ? 'step-in-progress' : 'step-complete' );
		}
	}
}
