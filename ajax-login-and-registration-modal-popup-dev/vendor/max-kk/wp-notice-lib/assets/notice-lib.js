jQuery(document).ready(function($) {
    // Send request to Dismiss Notice
    $(".wp-is-dismissible .notice-dismiss").click(function() {
        var dismiss_url = $(this).parent().data("dismiss-url")
        if ( dismiss_url ) {
            $.get( dismiss_url, function(resp){
                if ( ! resp.success ) {
                    alert( resp.data );
                } else {

                }
            });
        }
    });
});
