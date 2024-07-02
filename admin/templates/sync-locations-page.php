<?php 
$custom_logo = houzez_option( 'custom_logo', false, 'url' );
$logo_height = houzez_option('retina_logo_height');
$logo_width = houzez_option('retina_logo_width');
?>
<div class="aqar-wrap">
    <?php if( !empty( $custom_logo ) ) { ?>
        <img src="<?php echo esc_url( $custom_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>"
        width="<?php echo esc_attr($logo_width); ?>" alt="logo">
    <?php } ?>
    <h1>مزامنة مواقع العقارات</h1>
    <p>انقر فوق الزر أدناه لبدء عملية المزامنة لمواقع العقارات.</p>
    <button id="sync-locations-button" class="button button-primary">مزامنة المواقع</button>
    <div id="sync-locations-progress" style="margin-top: 20px;">
        <div class="progress mt-3">
            <div id="sync-locations-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p id="sync-locations-status"></p>
        <pre id="sync-locations-log"></pre>
    </div>
</div>

