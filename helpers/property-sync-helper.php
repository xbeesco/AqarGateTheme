<?php
/**
 * Shared Property Sync Helper Functions
 *
 * This file contains reusable functions for property synchronization
 * that can be used by both individual sync buttons and bulk sync operations
 *
 * @package AqarGateTheme
 */


/*
|--------------------------------------------------------------------------------------------
|   Build PlatformCompliance request body for sync (notify authority that we display the ad).
|   Used after successful sync when property is published.
|--------------------------------------------------------------------------------------------
*/
/**
 * @param int $post_id Property post ID (must have advertisement_response meta and author)
 * @return array|null Body as array for json_encode, or null if data missing
 */
function build_platform_compliance_body_for_sync( $post_id ) {
    $advertisement_response = get_post_meta( $post_id, 'advertisement_response', true );
    if ( empty( $advertisement_response ) || ! is_array( $advertisement_response ) ) {
        return null;
    }

    $ar                = $advertisement_response;
    $author            = ag_sync_get_author_identity( $post_id );
    $cleaned_phone     = $author['phone'];
    $user_title        = $author['title'];
    $display_name      = $author['display_name'];
    $price             = (int) get_post_meta( $post_id, 'fave_property_price', true );
    $ad_link           = html_entity_decode( get_the_permalink( $post_id ) );
    $ad_type           = ag_sync_map_ad_type( $ar );
    $property_type     = ag_sync_map_property_type( $ar );
    $property_details  = ag_sync_normalize_property_details( $ar );
    $location          = ag_sync_normalize_location( $ar );
    $formatted_borders = ag_sync_build_borders( $ar );

    $body = array(
        'adLicenseNumber'                    => $ar['adLicenseNumber'] ?? '',
        'adLicenseUrl'                       => $ad_link,
        'adLinkInPlatform'                   => $ad_link,
        'adSource'                           => 'AqarGate',
        'adType'                             => $ad_type,
        'advertiserId'                       => $ar['advertiserId'] ?? '',
        'advertiserMobile'                   => $cleaned_phone,
        'advertiserName'                     => $user_title,
        'borders'                            => $formatted_borders,
        'brokerageAndMarketingLicenseNumber' => $ar['brokerageAndMarketingLicenseNumber'] ?? '',
        'channels'                           => array( 'LicensedPlatform' ),
        'isConstrained'                      => $property_details['constraints'],
        'creationDate'                       => $ar['creationDate'] ?? '',
        'endDate'                            => $ar['endDate'] ?? '',
        'nationalAddress'                    => $location,
        'operationReason'                    => 'Other',
        'operationType'                      => 'DisplayAd',
        'platformId'                         => get_option( '_platformid', '' ),
        'platformOwnerId'                    => get_option( '_platformownerid', '' ),
        'price'                              => $price,
        'propertyArea'                       => $property_details['area'],
        'propertyType'                       => $property_type,
        'propertyUsage'                      => array( 'Residential' ),
        'propertyUtilities'                  => $property_details['utilities'],
        'qrCode'                             => '',
        'titleDeedNumber'                    => $ar['deedNumber'] ?? $ar['titleDeedNumber'] ?? '',
        'titleDeedType'                      => 'ElectronicDeed',
        'landTotalPrice'                     => $property_details['land_total_price'],
        'landTotalAnnualRent'                => '0',
        'ownershipTransferFeeType'           => 'المالك',
        'responsibleEmployeeName'            => $display_name,
        'responsibleEmployeePhoneNumber'     => $cleaned_phone,
        'notes'                              => $property_details['notes'],
        'titleDeedTypeName'                  => $property_details['title_deed_type_name'],
    );

    return $body;
}

/*
|--------------------------------------------------------------------------------------------
|   Get author identity (title, display name, cleaned phone) for a property post.
|--------------------------------------------------------------------------------------------
*/
function ag_sync_get_author_identity( $post_id ) {
    $user_id      = (int) get_post_field( 'post_author', $post_id );
    $user_title   = get_the_author_meta( 'fave_author_title', $user_id );
    $display_name = get_the_author_meta( 'aqar_display_name', $user_id );

    if ( empty( $display_name ) ) {
        $display_name = get_the_author_meta( 'display_name', $user_id );
    }
    if ( empty( $user_title ) ) {
        $user_title = $display_name;
    }

    $advertiser_mobile = get_the_author_meta( 'fave_author_mobile', $user_id );
    $pattern           = '/^\+\s*966/';
    $cleaned_phone     = preg_replace( $pattern, '', $advertiser_mobile );
    if ( substr( $cleaned_phone, 0, 1 ) !== '0' ) {
        $cleaned_phone = '0' . $cleaned_phone;
    }

    return array(
        'title'        => $user_title,
        'display_name' => $display_name,
        'phone'        => $cleaned_phone,
    );
}

/*
|--------------------------------------------------------------------------------------------
|   Map Arabic advertisementType to API adType (Rent/Sell).
|--------------------------------------------------------------------------------------------
*/
function ag_sync_map_ad_type( array $ar ) {
    $map       = array( 'إيجار' => 'Rent', 'بيع' => 'Sell' );
    $ad_type_ar = $ar['advertisementType'] ?? 'بيع';

    return isset( $map[ $ad_type_ar ] ) ? $map[ $ad_type_ar ] : 'Sell';
}

/*
|--------------------------------------------------------------------------------------------
|   Map Arabic propertyType to API propertyType.
|--------------------------------------------------------------------------------------------
*/
function ag_sync_map_property_type( array $ar ) {
    $map = array(
        'أرض' => 'Land',
        'ارض' => 'Land',
        'دور' => 'Floor',
        'شقة' => 'Apartment',
        'فيلا' => 'Villa',
        'شقَّة صغيرة (استوديو)' => 'Studio',
        'غرفة' => 'Room',
        'استراحة' => 'RestHouse',
        'مجمع' => 'Compound',
        'برج' => 'Tower',
        'معرض' => 'Exhibition',
        'مكتب' => 'Office',
        'مستودع' => 'Warehouses',
        'كشك' => 'Booth',
        'سينما' => 'Cinema',
        'فندق' => 'Hotel',
        'مواقف سيارات' => 'CarParking',
        'ورشة' => 'RepairShop',
        'صراف' => 'Teller',
        'مصنع' => 'Factory',
        'مدرسة' => 'School',
        'مستشفى، مركز صحي' => 'HospitalOrHealthCenter',
        'محطة كهرباء' => 'ElectricityStation',
        'برج اتصالات' => 'TelecomTower',
        'محطة' => 'Station',
        'مزرعة' => 'Farm',
        'عمارة' => 'Building',
    );

    $prop_type_ar = $ar['propertyType'] ?? 'ارض';

    return isset( $map[ $prop_type_ar ] ) ? $map[ $prop_type_ar ] : 'Land';
}

/*
|--------------------------------------------------------------------------------------------
|   Normalize property-level details: constraints flag, utilities, area and land_total_price.
|--------------------------------------------------------------------------------------------
*/
function ag_sync_normalize_property_details( array $ar ) {
    $utility_map = array(
        'كهرباء' => 'Electricity',
        'مياه' => 'Waters',
        'صرف صحي' => 'Sanitation',
        'لايوجد خدمات' => 'NoServices',
        'هاتف' => 'FixedPhone',
        'الياف ضوئية' => 'FibreOptics',
        'تصريف الفيضانات' => 'FloodDrainage',
    );

    $utilities = array();
    if ( ! empty( $ar['propertyUtilities'] ) && is_array( $ar['propertyUtilities'] ) ) {
        foreach ( $ar['propertyUtilities'] as $u ) {
            $utilities[] = isset( $utility_map[ $u ] ) ? $utility_map[ $u ] : 'NoServices';
        }
    }
    if ( empty( $utilities ) ) {
        $utilities[] = 'NoServices';
    }

    $constraints = 'False';
    if (
        ! empty( $ar['isConstrained'] )
        || ! empty( $ar['isPawned'] )
        || ! empty( $ar['isHalted'] )
        || ! empty( $ar['isTestment'] )
        || ! empty( $ar['rerConstraints'] )
    ) {
        $constraints = 'True';
    }

    $area            = isset( $ar['propertyArea'] ) ? (float) $ar['propertyArea'] : 0;
    $price_from_api  = isset( $ar['propertyPrice'] ) ? (float) $ar['propertyPrice'] : 0;
    $land_total      = ( $area > 0 && $price_from_api > 0 ) ? ( $price_from_api * $area ) : 0;
    $notes           = isset( $ar['notes'] ) ? $ar['notes'] : '';
    $title_deed_name = isset( $ar['titleDeedTypeName'] ) ? $ar['titleDeedTypeName'] : '';

    return array(
        'constraints'        => $constraints,
        'utilities'          => $utilities,
        'area'               => $area,
        'land_total_price'   => (string) $land_total,
        'notes'              => $notes,
        'title_deed_type_name' => $title_deed_name,
    );
}

/*
|--------------------------------------------------------------------------------------------
|   Normalize location structure for nationalAddress.
|--------------------------------------------------------------------------------------------
*/
function ag_sync_normalize_location( array $ar ) {
    $loc = isset( $ar['location'] ) && is_array( $ar['location'] ) ? $ar['location'] : array();

    return array(
        'additionalNo'   => $loc['additionalNumber'] ?? '',
        'adMapLatitude'  => $loc['latitude'] ?? null,
        'adMapLongitude' => $loc['longitude'] ?? null,
        'buildingNo'     => $loc['buildingNumber'] ?? '',
        'city'           => $loc['city'] ?? '',
        'district'       => $loc['district'] ?? '',
        'postalCode'     => $loc['postalCode'] ?? '',
        'region'         => $loc['region'] ?? '',
        'streetName'     => $loc['street'] ?? '',
    );
}

/*
|--------------------------------------------------------------------------------------------
|   Build borders array for PlatformCompliance from REGA borders object.
|--------------------------------------------------------------------------------------------
*/
function ag_sync_build_borders( array $ar ) {
    $borders = isset( $ar['borders'] ) ? $ar['borders'] : array();
    if ( is_object( $borders ) ) {
        $borders = (array) $borders;
    }

    $result = array();
    $dirs   = array(
        array(
            'desc_key' => 'northLimitDescription',
            'name_key' => 'northLimitName',
            'len_key'  => 'northLimitLengthChar',
        ),
        array(
            'desc_key' => 'eastLimitDescription',
            'name_key' => 'eastLimitName',
            'len_key'  => 'eastLimitLengthChar',
        ),
        array(
            'desc_key' => 'westLimitDescription',
            'name_key' => 'westLimitName',
            'len_key'  => 'westLimitLengthChar',
        ),
        array(
            'desc_key' => 'southLimitDescription',
            'name_key' => 'southLimitName',
            'len_key'  => 'southLimitLengthChar',
        ),
    );

    foreach ( $dirs as $d ) {
        if ( isset( $borders[ $d['name_key'] ] ) || isset( $borders[ $d['desc_key'] ] ) ) {
            $result[] = array(
                'direction' => $borders[ $d['desc_key'] ] ?? '',
                'type'      => $borders[ $d['name_key'] ] ?? '',
                'length'    => $borders[ $d['len_key'] ] ?? '',
            );
        }
    }

    return $result;
}

/**
 * Process single property sync with REGA
 *
 * @param int $post_id Property post ID
 * @return array Response array with success status and message
 */
function process_single_property_sync( $post_id ) {
    // Validate post ID
    if ( ! $post_id || get_post_type( $post_id ) !== 'property' ) {
        return array(
            'success' => false,
            'message' => 'Invalid property ID'
        );
    }

    // Get REGA metadata
    $advertiserId = get_post_meta( $post_id, 'advertiserId', true );
    $adLicenseNumber = get_post_meta( $post_id, 'adLicenseNumber', true );

    // Validate required metadata
    if ( empty( $advertiserId ) || empty( $adLicenseNumber ) ) {
        return array(
            'success' => false,
            'message' => 'Missing advertiserId or adLicenseNumber'
        );
    }

    // Initialize REGA module
    require_once AG_DIR . 'module/class-rega-module.php';
    $RegaMoudle = new RegaMoudle();

    // Call REGA API
    $response = $RegaMoudle->sysnc_AdvertisementValidator( $adLicenseNumber, $advertiserId );
    $response = json_decode( $response );

    // Handle invalid or failed API response
    if ( empty( $response ) || ! isset( $response->Header->Status->Code ) ) {
        return array(
            'success' => false,
            'message' => 'Invalid or empty response from REGA.'
        );
    }
    if ( $response->Header->Status->Code != 200 ) {
        $msg = "هنالك مشكلة في الاتصال مع هيئة العقار<br>";

        if ( isset( $response->Body->error->message ) ) {
            $msg .= $response->Body->error->message . '<br>';
        }

        if ( isset( $response->Header->Status->Description ) ) {
            $msg .= $response->Header->Status->Description . '<br>';
        }

        return array(
            'success' => false,
            'message' => $msg
        );
    }

    // Process valid response
    if ( isset( $response->Body->result->advertisement ) ) {
        $data = $response->Body->result->advertisement;

        /**
         * Save all REGA property data using the shared function
         * This ensures sync uses the same logic as initial property creation
         */
        $save_result = save_rega_property_data( $post_id, $data );

        if ( $save_result ) {
            // Verify directly that the expiry date has not passed the current time
            if ( !empty($data->endDate) ) {
                $dt = DateTime::createFromFormat('d/m/Y', trim($data->endDate), new DateTimeZone('UTC'));
                if ($dt === false) {
                    try { $dt = new DateTime(trim($data->endDate), new DateTimeZone('UTC')); } catch (Exception $e) {}
                }
                if ( $dt !== false && $dt->getTimestamp() < time() ) {
                    wp_update_post( array( 'ID' => $post_id, 'post_status' => 'expired' ) );
                    if ( function_exists( 'houzez_listing_expire_meta' ) ) {
                        houzez_listing_expire_meta( $post_id );
                    }
                    return array(
                        'success' => true,
                        'message' => 'تم إيقاف الإعلان لانتهاء صلاحيته بناءً على بيانات المزامنة: ' . $data->endDate,
                        'expired' => true
                    );
                }
            }

            // If property is published, notify the authority (PlatformCompliance) so REGA logs "إرسال/تحديث بيانات الإعلان"
            if ( get_post_status( $post_id ) === 'publish' ) {
                $compliance_body = build_platform_compliance_body_for_sync( $post_id );
                if ( $compliance_body !== null ) {
                    $RegaMoudle->current_ad_license_number = $post_id;
                    $RegaMoudle->PlatformCompliance( $compliance_body );
                }
            }

            return array(
                'success' => true,
                'message' => 'Data synchronized successfully!',
                'data' => $data
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Failed to save property data during sync!'
            );
        }
    }

    // Handle expired/invalid properties: authority said ad is no longer valid — only update locally, do not call PlatformCompliance
    elseif ( isset( $response->Body->result->isValid ) && $response->Body->result->isValid === false ) {
        wp_update_post( array(
            'ID' => $post_id,
            'post_status' => 'expired'
        ));

        if ( function_exists( 'houzez_listing_expire_meta' ) ) {
            houzez_listing_expire_meta( $post_id );
        }

        return array(
            'success' => true,
            'message' => $response->Body->result->message,
            'expired' => true
        );
    }

    // Handle other response types
    else {
        $message = isset( $response->Body->result->message ) ? $response->Body->result->message : 'Unknown error occurred';

        return array(
            'success' => false,
            'message' => $message
        );
    }
}

/**
 * Get properties for bulk sync based on filter
 *
 * @param string $filter Filter type: 'published', 'expired', or 'all'
 * @param int $limit Number of properties to fetch
 * @param int $offset Offset for pagination
 * @return array Array of property IDs
 */
function get_properties_for_sync( $filter = 'published', $limit = 20, $offset = 0 ) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => $limit,
        'offset' => $offset,
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'advertiserId',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => 'adLicenseNumber',
                'compare' => 'EXISTS'
            )
        )
    );

    // Apply filter
    switch ( $filter ) {
        case 'published':
            $args['post_status'] = 'publish';
            break;

        case 'expired':
            $args['post_status'] = array( 'expired', 'canceled' );
            break;

        case 'all':
            $args['post_status'] = array( 'publish', 'expired', 'canceled', 'pending' );
            break;

        default:
            $args['post_status'] = 'publish';
    }

    $query = new WP_Query( $args );
    return $query->posts;
}

/**
 * Get total count of properties for sync based on filter
 *
 * @param string $filter Filter type: 'published', 'expired', or 'all'
 * @return int Total count
 */
function get_properties_sync_count( $filter = 'published' ) {
    $args = array(
        'post_type' => 'property',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'advertiserId',
                'compare' => 'EXISTS'
            ),
            array(
                'key' => 'adLicenseNumber',
                'compare' => 'EXISTS'
            )
        )
    );

    // Apply filter
    switch ( $filter ) {
        case 'published':
            $args['post_status'] = 'publish';
            break;

        case 'expired':
            $args['post_status'] = array( 'expired', 'canceled' );
            break;

        case 'all':
            $args['post_status'] = array( 'publish', 'expired', 'canceled', 'pending' );
            break;

        default:
            $args['post_status'] = 'publish';
    }

    $query = new WP_Query( $args );
    return $query->found_posts;
}
