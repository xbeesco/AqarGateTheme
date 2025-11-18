jQuery(document).ready(function($) {
    let selectedPropertyId = null;
    let metaBefore = {};

    // Initialize Select2
    $('#property-select').select2({
        ajax: {
            url: singlePropSyncData.ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'search_properties',
                    search: params.term,
                    page: params.page || 1,
                    nonce: singlePropSyncData.nonce
                };
            },
            processResults: function(data) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        },
        placeholder: 'ابحث عن العقار بالاسم أو رقم ID...',
        minimumInputLength: 0,
        language: {
            inputTooShort: function() {
                return 'ابدأ بالكتابة للبحث...';
            },
            searching: function() {
                return 'جاري البحث...';
            },
            noResults: function() {
                return 'لم يتم العثور على نتائج';
            },
            loadingMore: function() {
                return 'جاري تحميل المزيد...';
            }
        },
        dir: 'rtl'
    });

    // Handle property selection
    $('#property-select').on('select2:select', function(e) {
        selectedPropertyId = e.params.data.id;
        $('#sync-single-btn').prop('disabled', false);

        // Load property details
        loadPropertyDetails(selectedPropertyId);
    });

    // Load property details before sync
    function loadPropertyDetails(propertyId) {
        $.ajax({
            url: singlePropSyncData.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_property_details',
                property_id: propertyId,
                nonce: singlePropSyncData.nonce
            },
            success: function(response) {
                if (response.success) {
                    metaBefore = response.data.meta_before;
                }
            }
        });
    }

    // Sync button click
    $('#sync-single-btn').on('click', function() {
        if (!selectedPropertyId) return;

        // Show loading
        $('#sync-loading').show();
        $('#sync-results').hide();
        $('#sync-single-btn').prop('disabled', true);

        // Run sync
        $.ajax({
            url: singlePropSyncData.ajaxurl,
            type: 'POST',
            data: {
                action: 'sync_single_property',
                property_id: selectedPropertyId,
                nonce: singlePropSyncData.nonce
            },
            success: function(response) {
                $('#sync-loading').hide();
                $('#sync-single-btn').prop('disabled', false);

                if (response.success) {
                    displayResults(response.data);
                } else {
                    alert('خطأ: ' + response.data.message);
                }
            },
            error: function(xhr, status, error) {
                $('#sync-loading').hide();
                $('#sync-single-btn').prop('disabled', false);
                alert('حدث خطأ في الاتصال: ' + error);
            }
        });
    });

    // Display results
    function displayResults(data) {
        $('#sync-results').show();
        $('#clear-results-btn').show();

        // Show status message
        const syncResult = data.sync_result;
        const statusBox = $('#sync-status-box');
        const statusMsg = $('#sync-status-message');

        if (syncResult.success) {
            statusBox.removeClass('notice-error').addClass('notice notice-success');
            statusMsg.html('<strong>✅ نجحت المزامنة!</strong> ' + syncResult.message +
                          ' <br><small>وقت التنفيذ: ' + data.execution_time + ' ثانية</small>');
        } else {
            statusBox.removeClass('notice-success').addClass('notice notice-error');
            statusMsg.html('<strong>❌ فشلت المزامنة!</strong> ' + syncResult.message);
        }
        statusBox.show();

        // Display property info
        displayPropertyInfo();

        // Display meta comparison
        displayMetaComparison(data.meta_after);

        // Display REGA data
        if (syncResult.data) {
            displayRegaData(syncResult.data);
        }

        // Scroll to results
        $('html, body').animate({
            scrollTop: $('#sync-results').offset().top - 50
        }, 500);
    }

    // Display property info
    function displayPropertyInfo() {
        const selectedOption = $('#property-select').select2('data')[0];
        const propertyInfo = `
            <tr>
                <th style="width: 200px;">رقم ID</th>
                <td>${selectedPropertyId}</td>
            </tr>
            <tr>
                <th>العنوان</th>
                <td>${selectedOption.text}</td>
            </tr>
            <tr>
                <th>رابط العقار</th>
                <td><a href="${singlePropSyncData.siteurl}/?p=${selectedPropertyId}" target="_blank">عرض العقار</a></td>
            </tr>
            <tr>
                <th>رابط التحرير</th>
                <td><a href="${singlePropSyncData.adminurl}/post.php?post=${selectedPropertyId}&action=edit" target="_blank">تحرير العقار</a></td>
            </tr>
        `;
        $('#property-info-table').html(propertyInfo);
    }

    // Display meta comparison
    function displayMetaComparison(metaAfter) {
        const metaKeys = {
            'advertiserId': 'معرف المعلن',
            'adLicenseNumber': 'رقم الترخيص الإعلاني',
            'responsibleEmployeeName': 'اسم المسؤول',
            'responsibleEmployeePhoneNumber': 'رقم المسؤول',
            'advertiserName': 'اسم المعلن',
            'phoneNumber': 'رقم الهاتف',
            'brokerageAndMarketingLicenseNumber': 'رقم ترخيص الوساطة',
            'propertyPrice': 'السعر',
            'propertyType': 'نوع العقار',
            'propertyAge': 'عمر العقار'
        };

        let beforeHtml = '';
        let afterHtml = '';

        Object.keys(metaKeys).forEach(function(key) {
            const label = metaKeys[key];
            const valueBefore = metaBefore[key] || '<em style="color: #999;">غير محدد</em>';
            const valueAfter = metaAfter[key] || '<em style="color: #999;">غير محدد</em>';

            const isChanged = valueBefore !== valueAfter;
            const changedClass = isChanged ? ' class="meta-changed"' : '';

            beforeHtml += `<tr${changedClass}>
                <td><strong>${label}</strong></td>
                <td>${valueBefore}</td>
            </tr>`;

            afterHtml += `<tr${changedClass}>
                <td><strong>${label}</strong></td>
                <td>${valueAfter}</td>
            </tr>`;
        });

        $('#meta-before-table').html(beforeHtml);
        $('#meta-after-table').html(afterHtml);
    }

    // Display REGA data
    function displayRegaData(regaData) {
        // Main data
        const mainFields = {
            'advertiserId': 'معرف المعلن',
            'adLicenseNumber': 'رقم الترخيص',
            'advertiserName': 'اسم المعلن',
            'responsibleEmployeeName': 'المسؤول',
            'responsibleEmployeePhoneNumber': 'رقم المسؤول',
            'phoneNumber': 'رقم الهاتف',
            'brokerageAndMarketingLicenseNumber': 'رقم الوساطة',
            'propertyType': 'نوع العقار',
            'propertyAge': 'عمر العقار',
            'propertyPrice': 'السعر',
            'propertyArea': 'المساحة',
            'numberOfRooms': 'عدد الغرف',
            'advertisementType': 'نوع الإعلان',
            'creationDate': 'تاريخ الإنشاء',
            'endDate': 'تاريخ الانتهاء'
        };

        let mainHtml = '';
        Object.keys(mainFields).forEach(function(key) {
            if (regaData[key] !== undefined) {
                mainHtml += `<tr>
                    <th style="width: 250px;">${mainFields[key]}</th>
                    <td>${regaData[key]}</td>
                </tr>`;
            }
        });
        $('#rega-main-data').html(mainHtml);

        // Location data
        if (regaData.location) {
            const location = regaData.location;
            const locationHtml = `
                <tr><th style="width: 250px;">المنطقة</th><td>${location.region || ''} (${location.regionCode || ''})</td></tr>
                <tr><th>المدينة</th><td>${location.city || ''} (${location.cityCode || ''})</td></tr>
                <tr><th>الحي</th><td>${location.district || ''} (${location.districtCode || ''})</td></tr>
                <tr><th>الشارع</th><td>${location.street || ''}</td></tr>
                <tr><th>الرمز البريدي</th><td>${location.postalCode || ''}</td></tr>
                <tr><th>رقم المبنى</th><td>${location.buildingNumber || ''}</td></tr>
                <tr><th>الإحداثيات</th><td>خط الطول: ${location.longitude || ''}, خط العرض: ${location.latitude || ''}</td></tr>
            `;
            $('#rega-location-data').html(locationHtml);
        }

        // Borders data
        if (regaData.borders) {
            const borders = regaData.borders;
            const bordersHtml = `
                <tr><th style="width: 250px;">الحد الشمالي</th><td>${borders.northLimitName || ''} ${borders.northLimitDescription || ''} (${borders.northLimitLengthChar || ''})</td></tr>
                <tr><th>الحد الشرقي</th><td>${borders.eastLimitName || ''} ${borders.eastLimitDescription || ''} (${borders.eastLimitLengthChar || ''})</td></tr>
                <tr><th>الحد الجنوبي</th><td>${borders.southLimitName || ''} ${borders.southLimitDescription || ''} (${borders.southLimitLengthChar || ''})</td></tr>
                <tr><th>الحد الغربي</th><td>${borders.westLimitName || ''} ${borders.westLimitDescription || ''} (${borders.westLimitLengthChar || ''})</td></tr>
            `;
            $('#rega-borders-data').html(bordersHtml);
        }

        // Full JSON
        $('#rega-full-json').text(JSON.stringify(regaData, null, 2));
    }

    // Toggle JSON
    $('#toggle-json-btn').on('click', function() {
        $('#rega-full-json').slideToggle();
    });

    // Copy JSON
    $('#copy-json-btn').on('click', function() {
        const jsonText = $('#rega-full-json').text();
        navigator.clipboard.writeText(jsonText).then(function() {
            alert('تم نسخ JSON إلى الحافظة');
        });
    });

    // Clear results
    $('#clear-results-btn').on('click', function() {
        $('#sync-results').hide();
        $('#clear-results-btn').hide();
        $('#property-select').val(null).trigger('change');
        selectedPropertyId = null;
        metaBefore = {};
        $('#sync-single-btn').prop('disabled', true);
    });
});
