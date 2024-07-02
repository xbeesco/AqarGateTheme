jQuery(document).ready(function($) {
    $('.sysnc_listing').click(function(e) {
        e.preventDefault();
        var postId = $(this).data('id');
        var $this = $(this);
        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'sync_advertisement',
                post_id: postId
            },
            beforeSend: function() {
                $this.find('.houzez-loader-js').addClass('loader-show');
            },
            success: function(response) {
                $this.find('.houzez-loader-js').removeClass('loader-show');
                $('#responseMessage').html(response.message);
                $('#responseModal').dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $(this).dialog("close");
                            if (response.success == true) {
                                window.location.reload(); // Reload the page if success
                            }
                        }
                    }
                });
            },
            error: function() {
                $('#responseMessage').html('Error syncing data.');
                $('#responseModal').dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $(this).dialog("close");
                        }
                    }
                });
            }
        });
    });
});
