<?php 
$args = array(
    'public'   => true,
    '_builtin' => false
); 
$output = 'names'; // or objects
$operator = 'and'; // 'and' or 'or'
$taxonomies = get_taxonomies($args, $output, $operator); 
$custom_logo = houzez_option( 'custom_logo', false, 'url' );
$logo_height = houzez_option('retina_logo_height');
$logo_width = houzez_option('retina_logo_width');
$allow_tax = ['property_state', 'property_city', 'property_area'];
?>
<div class="aqar-wrap">
    <?php if( !empty( $custom_logo ) ) { ?>
    <img src="<?php echo esc_url( $custom_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>"
        width="<?php echo esc_attr($logo_width); ?>" alt="logo">
    <?php } ?>
    <h4 class="m-2">حذف المناطق والمدن والاحياء</h4>
    <p class="text-danger">تحذير : يتم حذف كل المناطق او المدن او الاحياء كاملة لا يمكن الرجوع  </p>
<?php
if ($taxonomies) {
    echo '<form id="TermAjax" action="" method="post">
            <select id="taxonomy-select" name="taxonomy-select">';
				echo '<option value="0">Choose Taxonomy</option>';
			foreach ($taxonomies as $taxonomy) {
                if( in_array($taxonomy, $allow_tax) ) {
                    echo '<option value="' . $taxonomy . '">' . $taxonomy . '</option>';
                }
			}
    echo '</select>
          <input type="submit" class="button button-primary" value="DELETE ALL TERMS">
		  <span class="btn-loader houzez-loader-js"></span>
          </form>';    
}



function array_flatten($array) { 
    if (!is_array($array)) { 
        return FALSE; 
    } 
    $result = array(); 
    foreach ($array as $key => $value) { 
        if (is_array($value)) {
            $result = array_merge($result, array_flatten($value)); 
        } else { 
            $result[$key] = $value; 
        } 
    } 
    return $result; 
} 

?>
<div id="aqar-log" style="display:none;margin: 40px 20px 0 0;">
	<div id="progress-container" style="width: 100%; background-color: #ddd;">
		<div id="progress-bar" style="width: 0%; height: 30px; background-color: #4CAF50; text-align: center; line-height: 30px; color: white;">0%</div>
	</div>
	<div id="log" style="background-color: #f8f8f8; border: 1px solid #ccc; padding: 10px; margin-top: 20px; height: 350px; overflow-y: scroll;">
		<h4>Log Messages</h4>
	</div>
</div>

<script>
jQuery(function($) {
    var frm = $('#TermAjax');
    var offset = 0;
    var totalTermsProcessed = 0;
    var totalTerms = 0;

    function fetchTotalTermsAndStart() {
        var taxonomy_name = $('#taxonomy-select').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'get_total_terms',
                'taxonomy_name': taxonomy_name
            },
            success: function(response) {
                if (response.success) {
                    totalTerms = response.data.totalTerms;
                    TermAjax(); // Start the deletion process after getting total terms
                } else {
                    logMessage("Failed to retrieve total terms: " + response.message);
                }
            }
        });
    }

    function TermAjax() {
        var taxonomy_name = $('#taxonomy-select').val();
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'del_terms',
                'taxonomy_name': taxonomy_name,
                offset: offset
            },
            beforeSend: function(){
                logMessage("Starting AJAX request for " + taxonomy_name + " at offset " + offset);
            },
            success: function (response) {
                if (response.success) {
                    var processedCount = response.data.terms.length;
                    totalTermsProcessed += processedCount;
                    offset += processedCount;
                    updateProgressBar(totalTermsProcessed, totalTerms);
                    logMessage("Successfully processed " + processedCount + " terms. Terms: " + response.data.terms.join(", "));

                    if (response.data.continue) {
                        TermAjax(); // Recursive call to continue deletion
                    } else {
                        $('.sync__loader').hide();
                        $('.sync__msg').html("<p> Completed: " + response.data.message + " </p>").show();
                        logMessage("Deletion completed. Total terms processed: " + totalTermsProcessed);
                        resetProgressBar();
                    }
                } else {
                    $('.sync__loader').hide();
                    $('.sync__msg_error').html("<p> Error: " + response.message + " </p>").show();
                    logMessage("Error: " + response.message);
                    resetProgressBar();
                }
            },
            error: function(response) {  
                $('.sync__loader').hide();
                $('.sync__msg_error').html("<p> Error: " + response.responseText + " </p>").show();
                logMessage("Error: " + response.responseText);
                resetProgressBar();
            },
        });
    }

    function updateProgressBar(processed, total) {
        var progressPercentage = (processed / total) * 100;
        $('#progress-bar').width(progressPercentage + '%');
        $('#progress-bar').text(Math.round(progressPercentage) + '%');
    }

	function resetProgressBar() {
        $('#progress-bar').width('0%');
        $('#progress-bar').text('0%');
    }

	// Function to log messages in the log section
    function logMessage(message) {
        var now = new Date();
        var timestamp = now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
        var $log = $('#log');
        $log.append('<div>[' + timestamp + '] ' + message + '</div>');
        $log.scrollTop($log[0].scrollHeight);
    }

    frm.submit(function (e) {
        e.preventDefault();
        $('#aqar-log').show();
        offset = 0; // Reset offset
        totalTermsProcessed = 0; // Reset processed count
        fetchTotalTermsAndStart();
    });
});
</script>
</div>