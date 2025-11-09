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

    $('.sysnc_listing_redf').click(function(e) {
        e.preventDefault();
        var postId = $(this).data('id');
        var $this = $(this);
        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'REDF_SYNC',
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

jQuery(document).ready(function($) {
    $('.delete_listing').click(function(e) {
        e.preventDefault();
        var postId = $(this).data('id');
        var $this = $(this);
        if (confirm('هل أنت متأكد من حذف هذا الإعلان؟')) {
            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'aqargate_delete_api_property',
                    propID: postId
                },
                beforeSend: function() {
                    $this.find('.houzez-loader-js').addClass('loader-show');
                },
                success: function(response) {
                    $this.find('.houzez-loader-js').removeClass('loader-show');
                    $('#responseMessage').html(response.mesg);
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
                    $('#responseMessage').html('Error deleting property.');
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
        }
    });
});
