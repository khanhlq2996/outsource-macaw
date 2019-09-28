jQuery(document).ready(function( $ ) {
	$('.uk-form-custom select').change(function(){
		var value = $(this).val();
		
		if (value == 1) {
			$('.box_price1 p').html('$6');
		} else {
			$('.box_price1 p').html('$5');
		}
	})

    document.addEventListener( 'wpcf7mailsent', function( event ) {
        setTimeout(function(){
            window.location.replace(location.host + "/thank-you/");
        }, 100);
    }, true );
});