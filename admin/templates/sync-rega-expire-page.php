<?php
$custom_logo = houzez_option( 'custom_logo', false, 'url' );
$logo_height = houzez_option('retina_logo_height');
$logo_width = houzez_option('retina_logo_width');
$properties = get_posts(array(
        'post_type'      => 'property',
        'posts_per_page' => -1,
        'meta_key'       => 'advertisement_response',
        'meta_compare'   => 'EXISTS',
        'post_status'    => 'expired'
    ));

    $total_properties = count($properties);
   	
?>
<div class="aqar-wrap">
    <?php if( !empty( $custom_logo ) ) { ?>
        <img src="<?php echo esc_url( $custom_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>"
        width="<?php echo esc_attr($logo_width); ?>" alt="logo">
    <?php } ?>
    <h1>مزامنة العقارات المنتهية</h1>
     <p>عدد العقارات <?php echo  $total_properties ; ?></p>
    <p>انقر فوق الزر أدناه لبدء عملية المزامنة العقارات.</p>
    <button id="sync-expired-properties-button" class="button button-primary">مزامنة</button>
    <div id="sync-properties-progress" style="margin-top: 20px;">
        <div class="progress mt-3">
            <div id="sync-properties-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <p id="sync-properties-status"></p>
        <pre id="sync-properties-log"></pre>
    </div>
</div>