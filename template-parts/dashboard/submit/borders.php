<?php
global $is_multi_steps, $hide_prop_fields;
$property_borders = [];
if (houzez_edit_property()) {
    global $property_data;
    $property_borders = get_post_meta( $property_data->ID, 'borders', true );   
}

$disabled = 'disabled';
$readonly = 'readonly';

if( aqar_can_edit() ) {
    $disabled = '';
    $readonly = '';
}

?>
<div id="borders" class="dashboard-content-block-wrap <?php echo esc_attr($is_multi_steps);?>">
    <h2>حدود واطوال العقار من وزارة العدل</h2>
    <div class="dashboard-content-block">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="northLimitName">نوع الحد الشمالي</label>
                    <input type="text" id="northLimitName" class="form-control" value="<?php echo $property_borders['northLimitName'] ?? 'لا يوجد';  ?>" name="borders[northLimitName]" <?php echo $readonly; ?>>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="northLimitDescription">وصف الحد الشمالي</label>
                    <input type="text" id="northLimitDescription" class="form-control" value="<?php echo $property_borders['northLimitDescription'] ?? 'لا يوجد';  ?>" name="borders[northLimitDescription]" <?php echo $readonly; ?>>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="northLimitLengthChar">طول الحد الشمالي</label>
                    <input type="text" id="northLimitLengthChar" class="form-control" value="<?php echo $property_borders['northLimitLengthChar'] ?? 'لا يوجد';  ?>" name="borders[northLimitLengthChar]" <?php echo $readonly; ?>>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="eastLimitName">نوع الحد الشرقي</label>
                    <input type="text" id="eastLimitName" class="form-control" value="<?php echo $property_borders['eastLimitName'] ?? 'لا يوجد';  ?>" name="borders[eastLimitName]" <?php echo $readonly; ?>>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="eastLimitDescription">وصف الحد الشرقي</label>
                    <input type="text" id="eastLimitDescription" class="form-control" value="<?php echo $property_borders['eastLimitDescription'] ?? 'لا يوجد';  ?>" name="borders[eastLimitDescription]" <?php echo $readonly; ?>>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="eastLimitLengthChar">طول الحد الشرقي</label>
                    <input type="text" id="eastLimitLengthChar" class="form-control" value="<?php echo $property_borders['eastLimitLengthChar'] ?? 'لا يوجد';  ?>" name="borders[eastLimitLengthChar]" <?php echo $readonly; ?>>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="westLimitName">نوع الحد الغربي</label>
                    <input type="text" id="westLimitName" class="form-control" value="<?php echo $property_borders['westLimitName'] ?? 'لا يوجد';  ?>" name="borders[westLimitName]" <?php echo $readonly; ?>>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="westLimitDescription">وصف الحد الغربي</label>
                    <input type="text" id="westLimitDescription" class="form-control" value="<?php echo $property_borders['westLimitDescription'] ?? 'لا يوجد';  ?>" name="borders[westLimitDescription]" <?php echo $readonly; ?>>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="westLimitLengthChar">طول الحد الغربي</label>
                    <input type="text" id="westLimitLengthChar" class="form-control" value="<?php echo $property_borders['westLimitLengthChar'] ?? 'لا يوجد';  ?>" name="borders[westLimitLengthChar]" <?php echo $readonly; ?>>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="southLimitName">نوع الحد الجنوبي</label>
                    <input type="text" id="southLimitName" class="form-control" value="<?php echo $property_borders['southLimitName'] ?? 'لا يوجد';  ?>" name="borders[southLimitName]" <?php echo $readonly; ?>>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="southLimitDescription">وصف الحد الجنوبي</label>
                    <input type="text" id="southLimitDescription" class="form-control" value="<?php echo $property_borders['southLimitDescription'] ?? 'لا يوجد';  ?>" name="borders[southLimitDescription]" <?php echo $readonly; ?>>
                </div>
            </div>

            <div class="col-md-6 col-sm-12">
                <div class="form-group">
                    <label for="southLimitLengthChar">طول الحد الجنوبي</label>
                    <input type="text" id="southLimitLengthChar" class="form-control" value="<?php echo $property_borders['southLimitLengthChar'] ?? 'لا يوجد';  ?>" name="borders[southLimitLengthChar]" <?php echo $readonly; ?>>
                </div>
            </div>
        </div>
</div><!-- dashboard-content-block -->
</div><!-- dashboard-content-block-wrap -->