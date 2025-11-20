<?php
$custom_logo = houzez_option( 'custom_logo', false, 'url' );
$logo_height = houzez_option('retina_logo_height');
$logo_width = houzez_option('retina_logo_width');
?>
<div class="aqar-wrap">
    <?php if( !empty( $custom_logo ) ) { ?>
        <img src="<?php echo esc_url( $custom_logo ); ?>" height="<?php echo esc_attr($logo_height); ?>" width="<?php echo esc_attr($logo_width); ?>" alt="logo">
    <?php } ?>

    <h1><?php _e('إعادة مزامنة العقارات', 'aqar-gate'); ?></h1>

    <div class="props-resync-form" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 20px 0;">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="batch-size"><?php _e('عدد العقارات في المرة الواحدة', 'aqar-gate'); ?></label>
                </th>
                <td>
                    <input type="number" id="batch-size" name="batch_size" value="20" min="1" max="100" class="regular-text" />
                    <p class="description"><?php _e('عدد العقارات التي سيتم مزامنتها في كل دفعة', 'aqar-gate'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="property-filter"><?php _e('نوع العقارات', 'aqar-gate'); ?></label>
                </th>
                <td>
                    <select id="property-filter" name="property_filter" class="regular-text">
                        <option value="published"><?php _e('العقارات المنشورة (Published)', 'aqar-gate'); ?></option>
                        <option value="expired"><?php _e('العقارات المنتهية (Expired/Canceled)', 'aqar-gate'); ?></option>
                        <option value="all"><?php _e('جميع العقارات', 'aqar-gate'); ?></option>
                    </select>
                    <p class="description"><?php _e('اختر نوع العقارات المراد مزامنتها', 'aqar-gate'); ?></p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="button" id="start-resync-btn" class="button button-primary button-large">
                <span class="dashicons dashicons-update" style="margin-top: 3px;"></span>
                <?php _e('بدء المزامنة', 'aqar-gate'); ?>
            </button>
            <button type="button" id="stop-resync-btn" class="button button-secondary button-large" style="display: none;">
                <span class="dashicons dashicons-no" style="margin-top: 3px;"></span>
                <?php _e('إيقاف المزامنة', 'aqar-gate'); ?>
            </button>
            <button type="button" id="resume-resync-btn" class="button button-primary button-large" style="display: none;">
                <span class="dashicons dashicons-controls-play" style="margin-top: 3px;"></span>
                <?php _e('استكمال المزامنة', 'aqar-gate'); ?>
            </button>
        </p>
    </div>

    <!-- Progress Section -->
    <div id="resync-progress-section" style="display: none; background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 20px 0;">
        <h2><?php _e('تقدم العملية', 'aqar-gate'); ?></h2>

        <div style="margin: 20px 0;">
            <div class="progress-bar-container" style="background: #f0f0f1; border-radius: 4px; height: 30px; position: relative; overflow: hidden;">
                <div id="resync-progress-bar" style="background: linear-gradient(90deg, #2271b1 0%, #135e96 100%); height: 100%; width: 0%; transition: width 0.3s; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 14px;">
                    <span id="resync-progress-text">0%</span>
                </div>
            </div>
        </div>

        <div class="resync-stats" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin: 20px 0;">
            <div style="padding: 15px; background: #e7f5fe; border-left: 4px solid #2271b1; border-radius: 4px;">
                <div style="font-size: 12px; color: #646970; margin-bottom: 5px;"><?php _e('العقارات المتزامنة', 'aqar-gate'); ?></div>
                <div style="font-size: 24px; font-weight: bold; color: #2271b1;">
                    <span id="synced-count">0</span> / <span id="total-count">0</span>
                </div>
            </div>

            <div style="padding: 15px; background: #fcf3ef; border-left: 4px solid #d63638; border-radius: 4px;">
                <div style="font-size: 12px; color: #646970; margin-bottom: 5px;"><?php _e('العقارات الفاشلة', 'aqar-gate'); ?></div>
                <div style="font-size: 24px; font-weight: bold; color: #d63638;">
                    <span id="failed-count">0</span>
                </div>
            </div>

            <div style="padding: 15px; background: #f0f6fc; border-left: 4px solid #50575e; border-radius: 4px;">
                <div style="font-size: 12px; color: #646970; margin-bottom: 5px;"><?php _e('الوقت المتبقي التقريبي', 'aqar-gate'); ?></div>
                <div style="font-size: 24px; font-weight: bold; color: #50575e;">
                    <span id="estimated-time">--:--</span>
                </div>
            </div>
        </div>

        <div id="resync-status" style="padding: 10px; background: #f0f6fc; border-radius: 4px; margin: 10px 0; font-size: 14px;">
            <?php _e('جاهز للبدء...', 'aqar-gate'); ?>
        </div>
    </div>

    <!-- Log Section -->
    <div id="resync-log-section" style="display: none; background: #fff; padding: 20px; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 20px 0;">
        <h2><?php _e('سجل العمليات', 'aqar-gate'); ?></h2>


        <div id="resync-log-container" style="max-height: 400px; overflow-y: auto; border: 1px solid #dcdcde; border-radius: 4px; background: #f6f7f7;">
            <table class="wp-list-table widefat fixed striped" style="margin: 0;">
                <thead>
                    <tr>
                        <th style="width: 60px;"><?php _e('#', 'aqar-gate'); ?></th>
                        <th style="width: 80px;"><?php _e('ID', 'aqar-gate'); ?></th>
                        <th><?php _e('عنوان العقار', 'aqar-gate'); ?></th>
                        <th style="width: 100px;"><?php _e('الحالة', 'aqar-gate'); ?></th>
                        <th><?php _e('الرسالة', 'aqar-gate'); ?></th>
                        <th style="width: 100px;"><?php _e('الوقت', 'aqar-gate'); ?></th>
                    </tr>
                </thead>
                <tbody id="resync-log-tbody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #646970;">
                            <?php _e('لا توجد عمليات بعد', 'aqar-gate'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.aqar-wrap {
    max-width: 1200px;
    margin: 20px auto;
}
#resync-log-tbody tr td {
    padding: 8px;
}
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
}
.status-success {
    background: #d1e7dd;
    color: #0f5132;
}
.status-error {
    background: #f8d7da;
    color: #842029;
}
.status-duplicate {
    background: #fff3cd;
    color: #856404;
}
.property-link {
    color: #2271b1;
    text-decoration: none;
}
.property-link:hover {
    color: #135e96;
    text-decoration: underline;
}
</style>
