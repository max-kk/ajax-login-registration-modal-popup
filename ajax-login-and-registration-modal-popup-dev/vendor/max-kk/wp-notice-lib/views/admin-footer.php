<script type="text/javascript">
jQuery(document).ready(function($) {
    // Send request for Dismiss Notice
    $(document).on("click", ".wp-is-dismissible .notice-dismiss", function(e) {
//jQuery(".wp-is-dismissible .notice-dismiss").click(function() {
        var dismiss_url = jQuery(this).parent().data("dismiss-url")
        if ( ! dismiss_url ) { return; }

	    jQuery.get( dismiss_url, function(r){
            if ( ! r.success ) {
                alert( r.data );
            }
        });
    });
});
</script>