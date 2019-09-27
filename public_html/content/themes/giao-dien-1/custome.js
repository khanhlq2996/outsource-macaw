jQuery(document).ready(function( $ ) {

    document.addEventListener( 'wpcf7mailsent', function( event ) {
        setTimeout(function(){
            window.location.replace("https://macaw.khanhlq.com/thank-you/");
        }, 100);
    }, true );
});