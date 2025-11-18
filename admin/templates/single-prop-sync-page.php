<?php
$custom_logo = houzez_option( 'custom_logo', false, 'url' );
$logo_height = houzez_option('retina_logo_height');
$logo_width = houzez_option('retina_logo_width');
?>
<div class="aqar-wrap single-prop-sync-wrap">
    <?php if( !empty( $custom_logo ) ) { ?>
        <img src="<?php echo esc_url( $custom_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>" width="<?php echo esc_attr($logo_width); ?>" alt="logo">
    <?php } ?>

    <h1><?php _e('مزامنة عقار واحد', 'aqar-gate'); ?></h1>
    <p class="description"><?php _e('ابحث عن العقار المراد مزامنته وشاهد النتائج التفصيلية', 'aqar-gate'); ?></p>

    <!-- Property Selection Section -->
    <div class="single-sync-form" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 20px 0;">
        <h2><?php _e('اختر العقار', 'aqar-gate'); ?></h2>

        <div style="margin: 20px 0;">
            <label for="property-select" style="display: block; margin-bottom: 10px; font-weight: 600;">
                <?php _e('ابحث عن العقار (بالاسم أو رقم ID)', 'aqar-gate'); ?>
            </label>
            <select id="property-select" style="width: 100%; max-width: 600px;"></select>
            <p class="description" style="margin-top: 8px;">
                <?php _e('ابدأ بالكتابة للبحث عن العقار، أو اكتب رقم ID مباشرة', 'aqar-gate'); ?>
            </p>
        </div>

        <div style="margin: 20px 0;">
            <button type="button" id="sync-single-btn" class="button button-primary button-large" disabled>
                <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                <?php _e('مزامنة العقار المحدد', 'aqar-gate'); ?>
            </button>
            <button type="button" id="clear-results-btn" class="button button-secondary" style="display: none;">
                <span class="dashicons dashicons-dismiss"></span>
                <?php _e('مسح النتائج', 'aqar-gate'); ?>
            </button>
        </div>
    </div>

    <!-- Loading Section -->
    <div id="sync-loading" style="display: none; text-align: center; padding: 40px; background: #fff; border: 1px solid #ccd0d4; margin: 20px 0;">
        <div class="spinner" style="visibility: visible; float: none; margin: 0 auto 15px;"></div>
        <p style="font-size: 16px; color: #646970;"><?php _e('جاري المزامنة...', 'aqar-gate'); ?></p>
    </div>

    <!-- Results Section -->
    <div id="sync-results" style="display: none;">

        <!-- Status Message -->
        <div id="sync-status-box" class="notice" style="margin: 20px 0; padding: 15px; display: none;">
            <p id="sync-status-message" style="margin: 0; font-size: 16px;"></p>
        </div>

        <!-- Property Info -->
        <div class="results-section" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; margin: 20px 0;">
            <h2><?php _e('معلومات العقار', 'aqar-gate'); ?></h2>
            <table class="widefat fixed striped">
                <tbody id="property-info-table">
                    <!-- Will be populated by JS -->
                </tbody>
            </table>
        </div>

        <!-- Meta Data Comparison -->
        <div class="results-section" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; margin: 20px 0;">
            <h2><?php _e('البيانات الوصفية', 'aqar-gate'); ?></h2>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Before Sync -->
                <div>
                    <h3 style="color: #d63638;"><?php _e('قبل المزامنة', 'aqar-gate'); ?></h3>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('الحقل', 'aqar-gate'); ?></th>
                                <th><?php _e('القيمة', 'aqar-gate'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="meta-before-table">
                            <!-- Will be populated by JS -->
                        </tbody>
                    </table>
                </div>

                <!-- After Sync -->
                <div>
                    <h3 style="color: #00a32a;"><?php _e('بعد المزامنة', 'aqar-gate'); ?></h3>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('الحقل', 'aqar-gate'); ?></th>
                                <th><?php _e('القيمة', 'aqar-gate'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="meta-after-table">
                            <!-- Will be populated by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- REGA API Response -->
        <div class="results-section" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; margin: 20px 0;">
            <h2><?php _e('استجابة REGA API', 'aqar-gate'); ?></h2>

            <!-- Main Data -->
            <div style="margin-bottom: 20px;">
                <h3><?php _e('البيانات الأساسية', 'aqar-gate'); ?></h3>
                <table class="widefat striped">
                    <tbody id="rega-main-data">
                        <!-- Will be populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Location Data -->
            <div style="margin-bottom: 20px;">
                <h3><?php _e('بيانات الموقع', 'aqar-gate'); ?></h3>
                <table class="widefat striped">
                    <tbody id="rega-location-data">
                        <!-- Will be populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Borders Data -->
            <div style="margin-bottom: 20px;">
                <h3><?php _e('الحدود', 'aqar-gate'); ?></h3>
                <table class="widefat striped">
                    <tbody id="rega-borders-data">
                        <!-- Will be populated by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Full JSON Response -->
            <div>
                <h3><?php _e('الاستجابة الكاملة (JSON)', 'aqar-gate'); ?></h3>
                <button type="button" id="toggle-json-btn" class="button" style="margin-bottom: 10px;">
                    <span class="dashicons dashicons-visibility"></span>
                    <?php _e('عرض/إخفاء JSON', 'aqar-gate'); ?>
                </button>
                <button type="button" id="copy-json-btn" class="button" style="margin-bottom: 10px;">
                    <span class="dashicons dashicons-clipboard"></span>
                    <?php _e('نسخ JSON', 'aqar-gate'); ?>
                </button>
                <pre id="rega-full-json" style="display: none; background: #1d2327; color: #f0f0f1; padding: 20px; border-radius: 4px; overflow-x: auto; max-height: 500px; font-size: 13px; line-height: 1.6;"></pre>
            </div>
        </div>

    </div>
</div>

<style>
.single-prop-sync-wrap {
    max-width: 1400px;
    margin: 20px auto;
}
.results-section {
    box-shadow: 0 1px 3px rgba(0,0,0,.1);
    border-radius: 4px;
}
.results-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #2271b1;
    color: #1d2327;
}
.results-section h3 {
    margin: 15px 0 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #dcdcde;
}
.notice.notice-success {
    border-left-color: #00a32a;
    background: #edfaef;
}
.notice.notice-error {
    border-left-color: #d63638;
    background: #fcf0f1;
}
.widefat td {
    padding: 10px;
}
.widefat th {
    background: #f0f0f1;
    font-weight: 600;
}
.meta-changed {
    background: #fff3cd !important;
    font-weight: 600;
}
.select2-container {
    font-size: 14px;
}
</style>
