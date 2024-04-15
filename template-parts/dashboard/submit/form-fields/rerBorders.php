<div id="borders" class="dashboard-content-block-wrap <?php echo esc_attr($is_multi_steps);?>">
    <h2>حدود واطوال العقار من السجل العقاري</h2>
    <div class="dashboard-content-block">
<table class="dashboard-table additional-details-table">
	<thead>
		<tr>
			<td>
				<label>الاتجاة</label>
			</td>
			<td>
				<label>نوع الحد</label>
			</td>
            <td>
				<label>طول الحد</label>
			</td>
			<td></td>
			<td></td>
		</tr>
	</thead>
	<tbody id="rer-borders">
		<?php
		$data_increment = 0;
		if(houzez_edit_property()) {
			global $property_data;
			$rerBorders = get_post_meta( $property_data->ID, 'rerBorders', true );
			$count = 0;

			if( !empty($rerBorders) ) {
                foreach ($rerBorders as $add_feature): 
                	$direction = $add_feature['direction'] ?? '';
                	$type = $add_feature['type'] ?? '';
                	$length = $add_feature['length'] ?? '';

                	?>

                	<tr>
						<td class="table-q-width">
							<input class="form-control" name="rerBorders[<?php echo esc_attr( $count ); ?>][direction]" placeholder="" type="text" value="<?php echo esc_attr($direction); ?>">
						</td>
						<td class="table-q-width">
							<input class="form-control" name="rerBorders[<?php echo esc_attr( $count ); ?>][type]" placeholder="" type="text" value="<?php echo esc_attr($type); ?>">
						</td>
						<td class="table-q-width">
							<input class="form-control" name="rerBorders[<?php echo esc_attr( $count ); ?>][length]" placeholder="" type="text" value="<?php echo esc_attr($length); ?>">
						</td>
						<td>
							<button data-remove="<?php echo esc_attr( $count ); ?>" class="remove-additional-row btn btn-light-grey-outlined"><i class="houzez-icon icon-close"></i></button>
						</td>
					</tr>
            <?php
                	$count++;
                endforeach;
            }

            $data_increment = $count - 1;
		} else {
		?>
		<tr>
			<td class="table-q-width">
				<input class="form-control" name="rerBorders[0][direction]" placeholder="" type="text">
			</td>
			<td class="table-q-width">
				<input class="form-control" name="rerBorders[0][type]" placeholder="" type="text">
			</td>
            <td class="table-q-width">
				<input class="form-control" name="rerBorders[0][length]" placeholder="" type="text">
			</td>
			<td class="">
				<a class="sort-additional-row btn btn-light-grey-outlined"><i class="houzez-icon icon-navigation-menu"></i></a>
			</td>
			<td>
				<button data-remove="0" class="remove-additional-row btn btn-light-grey-outlined"><i class="houzez-icon icon-close"></i></button>
			</td>
		</tr>
	<?php } ?>
	</tbody>
    <tfoot>
		<tr >
			<td colspan="4">
				<button data-increment="<?php echo esc_attr($data_increment); ?>" class="add-additional-row btn btn-primary btn-left-icon mt-2"><i class="houzez-icon icon-add-circle"></i> <?php esc_html_e( 'Add New', 'houzez' ); ?></button>
			</td>
		</tr>
	</tfoot>
	</tbody>
</table>
    </div>
</div>