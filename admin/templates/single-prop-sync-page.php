<?php
$custom_logo = houzez_option( "custom_logo", false, "url" );
$logo_height = houzez_option("retina_logo_height");
$logo_width = houzez_option("retina_logo_width");
$url_property_id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
?>
<div class="aqar-wrap single-prop-sync-wrap">
    <?php if( !empty( $custom_logo ) ) { ?>
        <img src="<?php echo esc_url( $custom_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>" width="<?php echo esc_attr($logo_width); ?>" alt="logo">
    <?php } ?>

    <h1><?php _e("مزامنة عقار واحد", "aqar-gate"); ?></h1>

    <!-- Auto Loading Section -->
    <div id="auto-loading-section" class="<?php echo $url_property_id ? "" : "hidden"; ?>" style="background: #fff; border: 1px solid #ccd0d4; padding: 30px; border-radius: 8px; margin: 20px 0; color: #1d2327;">
        <div style="text-align: center;">
            <h2 id="auto-loading-title" style="color: #1d2327; margin: 0 0 10px;">جاري تحميل العقار رقم <?php echo $url_property_id; ?></h2>
            <p id="auto-loading-status" style="margin: 0 0 20px; color: #646970;">جاري الاتصال بالخادم...</p>
            <div style="background: #dcdcde; border-radius: 20px; height: 8px; max-width: 400px; margin: 0 auto;">
                <div id="auto-loading-progress" style="background: #2271b1; height: 100%; border-radius: 20px; width: 10%; transition: width 0.3s ease;"></div>
            </div>
        </div>
    </div>

    <!-- Manual Selection Section -->
    <div id="manual-selection-section" class="single-sync-form <?php echo $url_property_id ? "hidden" : ""; ?>" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 20px 0;">
        <h2><?php _e("اختر العقار", "aqar-gate"); ?></h2>
        <div style="margin: 20px 0;">
            <label for="property-select" style="display: block; margin-bottom: 10px; font-weight: 600;">
                <?php _e("ابحث عن العقار (بالاسم أو رقم ID)", "aqar-gate"); ?>
            </label>
            <select id="property-select" style="width: 100%; max-width: 600px;"></select>
        </div>
        <div style="margin: 20px 0;">
            <button type="button" id="sync-single-btn" class="button button-primary button-large" disabled>
                <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                <?php _e("مزامنة العقار المحدد", "aqar-gate"); ?>
            </button>
        </div>
    </div>

    <!-- Manual Loading -->
    <div id="sync-loading" class="hidden" style="text-align: center; padding: 40px; background: #fff; border: 1px solid #ccd0d4; margin: 20px 0; border-radius: 8px;">
        <div class="spinner" style="visibility: visible; float: none; margin: 0 auto 15px;"></div>
        <p style="font-size: 16px; color: #646970;">جاري المزامنة...</p>
    </div>

    <!-- Results Section -->
    <div id="sync-results" class="hidden">
        <!-- Status Message -->
        <div id="sync-status-box" class="notice hidden" style="margin: 20px 0; padding: 15px;">
            <p id="sync-status-message" style="margin: 0; font-size: 16px;"></p>
        </div>

        <!-- Property Info Card -->
        <div class="results-section property-card" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; margin: 20px 0;">
            <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h2 id="property-title-display" style="margin: 0 0 10px; color: #1d2327;"></h2>
                    <div id="property-meta-display" style="color: #646970;"></div>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" id="resync-btn" class="button button-primary">
                        <span class="dashicons dashicons-update" style="margin-top: 3px;"></span> مزامنة مرة أخرى
                    </button>
                    <button type="button" id="new-property-btn" class="button button-secondary">
                        <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span> عقار آخر
                    </button>
                </div>
            </div>
        </div>

        <!-- Changes Summary -->
        <div id="changes-section" class="results-section" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; margin: 20px 0;">
            <h2 style="display: flex; align-items: center; gap: 10px;">
                <span>📊</span> <span>التغييرات</span>
                <span id="changes-count" class="changes-badge">0</span>
            </h2>
            
            <!-- New Values -->
            <div id="new-values-section" class="change-group hidden">
                <h3 style="color: #00a32a;">🆕 قيم جديدة</h3>
                <table class="widefat striped"><thead><tr><th style="width: 30%;">الحقل</th><th>القيمة الجديدة</th></tr></thead>
                    <tbody id="new-values-table"></tbody>
                </table>
            </div>

            <!-- Modified Values -->
            <div id="modified-values-section" class="change-group hidden">
                <h3 style="color: #dba617;">✏️ قيم معدّلة</h3>
                <table class="widefat striped"><thead><tr><th style="width: 30%;">الحقل</th><th style="width: 35%;">القديمة</th><th style="width: 35%;">الجديدة</th></tr></thead>
                    <tbody id="modified-values-table"></tbody>
                </table>
            </div>

            <!-- Deleted Values -->
            <div id="deleted-values-section" class="change-group hidden">
                <h3 style="color: #d63638;">🗑️ قيم محذوفة</h3>
                <table class="widefat striped"><thead><tr><th style="width: 30%;">الحقل</th><th>القيمة المحذوفة</th></tr></thead>
                    <tbody id="deleted-values-table"></tbody>
                </table>
            </div>

            <!-- No Changes -->
            <div id="no-changes-message" class="hidden" style="text-align: center; padding: 30px; color: #646970;">
                <p style="font-size: 16px; margin-top: 15px;">لا توجد تغييرات - البيانات متطابقة</p>
            </div>
        </div>

        <!-- Full JSON -->
        <div class="results-section" style="background: #fff; padding: 25px; border: 1px solid #ccd0d4; margin: 20px 0;">
            <h2>الاستجابة الكاملة</h2>
            <div style="margin-bottom: 15px;">
                <button type="button" id="toggle-json-btn" class="button"><span class="dashicons dashicons-visibility" style="margin-top: 3px;"></span> عرض/إخفاء</button>
                <button type="button" id="copy-json-btn" class="button"><span class="dashicons dashicons-clipboard" style="margin-top: 3px;"></span> نسخ</button>
            </div>
            <pre id="rega-full-json" class="hidden" style="background: #1d2327; color: #f0f0f1; padding: 20px; border-radius: 4px; overflow-x: auto; max-height: 500px; font-size: 13px; line-height: 1.6; direction: ltr; text-align: left;"></pre>
        </div>
    </div>
</div>

<style>
.single-prop-sync-wrap { max-width: 1200px; margin: 20px auto; }
.hidden { display: none !important; }
.results-section { box-shadow: 0 1px 3px rgba(0,0,0,.1); border-radius: 8px; }
.results-section h2 { margin-top: 0; padding-bottom: 10px; border-bottom: 2px solid #2271b1; color: #1d2327; }
.results-section h3 { margin: 20px 0 10px; padding-bottom: 8px; border-bottom: 1px solid #dcdcde; }
.change-group { margin-bottom: 25px; }
.changes-badge { background: #2271b1; color: #1d2327; padding: 2px 10px; border-radius: 12px; font-size: 14px; }
.notice.notice-success { border-left-color: #00a32a; background: #edfaef; }
.notice.notice-error { border-left-color: #d63638; background: #fcf0f1; }
.widefat td, .widefat th { padding: 12px 15px; }
.widefat th { background: #f0f0f1; font-weight: 600; }
.value-new { background: #edfaef !important; }
.value-modified-old { background: #fcf0f1 !important; text-decoration: line-through; color: #999; }
.value-modified-new { background: #fff8e5 !important; }
.value-deleted { background: #fcf0f1 !important; color: #d63638; }
.value-array { font-family: monospace; background: #f0f0f1; padding: 3px 8px; border-radius: 4px; display: inline-block; margin: 2px; font-size: 12px; }
.value-null { color: #999; font-style: italic; }
.value-boolean-true { color: #00a32a; font-weight: 600; }
.value-boolean-false { color: #d63638; font-weight: 600; }
.property-card h2 { border-bottom: none; padding-bottom: 0; }
.select2-container { font-size: 14px; }
.field-path { color: #646970; font-size: 12px; display: block; margin-top: 3px; }
</style>
