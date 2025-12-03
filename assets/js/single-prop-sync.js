jQuery(document).ready(function($) {
    let selectedPropertyId = null;
    let oldRegaData = null;
    let currentPropertyTitle = "";

    const fieldLabels = {
        "advertiserId": "معرف المعلن",
        "adLicenseNumber": "رقم ترخيص الإعلان",
        "deedNumber": "رقم الصك",
        "advertiserName": "اسم المعلن",
        "responsibleEmployeeName": "اسم مسؤول الإعلان",
        "responsibleEmployeePhoneNumber": "رقم مسؤول الإعلان",
        "phoneNumber": "رقم الهاتف",
        "brokerageAndMarketingLicenseNumber": "رقم رخصة الوساطة",
        "isConstrained": "مقيد",
        "isPawned": "مرهون",
        "isHalted": "موقوف",
        "isTestment": "وصية",
        "streetWidth": "عرض الشارع",
        "propertyArea": "مساحة العقار",
        "propertyPrice": "السعر",
        "landTotalPrice": "سعر الأرض",
        "landTotalAnnualRent": "الإيجار السنوي",
        "numberOfRooms": "عدد الغرف",
        "propertyType": "نوع العقار",
        "propertyAge": "عمر العقار",
        "advertisementType": "نوع الإعلان",
        "propertyFace": "واجهة العقار",
        "planNumber": "رقم المخطط",
        "landNumber": "رقم الأرض",
        "creationDate": "تاريخ الإنشاء",
        "endDate": "تاريخ الانتهاء",
        "adLicenseUrl": "رابط الترخيص",
        "adSource": "مصدر الإعلان",
        "titleDeedTypeName": "نوع الصك",
        "locationDescriptionOnMOJDeed": "وصف الموقع في الصك",
        "notes": "ملاحظات",
        "mainLandUseTypeName": "نوع استخدام الأرض",
        "redZoneTypeName": "نوع المنطقة الحمراء",
        "ownershipTransferFeeType": "رسوم نقل الملكية",
        "propertyUsages": "استخدامات العقار",
        "propertyUtilities": "خدمات العقار",
        "location.region": "المنطقة",
        "location.city": "المدينة", 
        "location.district": "الحي",
        "location.street": "الشارع",
        "location.postalCode": "الرمز البريدي",
        "location.buildingNumber": "رقم المبنى",
        "location.longitude": "خط الطول",
        "location.latitude": "خط العرض",
        "borders.northLimitName": "الحد الشمالي",
        "borders.northLimitDescription": "وصف الحد الشمالي",
        "borders.northLimitLengthChar": "طول الحد الشمالي",
        "borders.eastLimitName": "الحد الشرقي",
        "borders.eastLimitDescription": "وصف الحد الشرقي",
        "borders.eastLimitLengthChar": "طول الحد الشرقي",
        "borders.southLimitName": "الحد الجنوبي",
        "borders.southLimitDescription": "وصف الحد الجنوبي",
        "borders.southLimitLengthChar": "طول الحد الجنوبي",
        "borders.westLimitName": "الحد الغربي",
        "borders.westLimitDescription": "وصف الحد الغربي",
        "borders.westLimitLengthChar": "طول الحد الغربي"
    };

    const urlParams = new URLSearchParams(window.location.search);
    const urlPropertyId = urlParams.get("id");

    // Flatten nested object
    function flattenObject(obj, prefix) {
        prefix = prefix || "";
        var result = {};
        for (var key in obj) {
            if (!obj.hasOwnProperty(key)) continue;
            var newKey = prefix ? prefix + "." + key : key;
            var value = obj[key];
            if (value !== null && typeof value === "object" && !Array.isArray(value)) {
                Object.assign(result, flattenObject(value, newKey));
            } else {
                result[newKey] = value;
            }
        }
        return result;
    }

    function formatValue(value) {
        if (value === null) return "<span class=\"value-null\">null</span>";
        if (value === undefined || value === "") return "<span class=\"value-null\">غير محدد</span>";
        if (typeof value === "boolean") {
            return value ? "<span class=\"value-boolean-true\">✓ نعم</span>" : "<span class=\"value-boolean-false\">✗ لا</span>";
        }
        if (Array.isArray(value)) {
            if (value.length === 0) return "<span class=\"value-null\">[ ] فارغ</span>";
            return value.map(function(item) {
                return "<span class=\"value-array\">" + escapeHtml(String(item)) + "</span>";
            }).join(" ");
        }
        if (typeof value === "number") return "<strong>" + value.toLocaleString("ar-SA") + "</strong>";
        return escapeHtml(String(value));
    }

    function escapeHtml(str) {
        var div = document.createElement("div");
        div.textContent = str;
        return div.innerHTML;
    }

    function isEmpty(value) {
        if (value === null || value === undefined || value === "") return true;
        if (Array.isArray(value) && value.length === 0) return true;
        return false;
    }

    function valuesEqual(a, b) {
        if (a === b) return true;
        if (isEmpty(a) && isEmpty(b)) return true;
        if (Array.isArray(a) && Array.isArray(b)) {
            return JSON.stringify(a.sort()) === JSON.stringify(b.sort());
        }
        return String(a) === String(b);
    }

    function getFieldLabel(key) {
        return fieldLabels[key] || key;
    }

    // Compare flattened data
    function compareData(oldData, newData) {
        var flatOld = flattenObject(oldData || {});
        var flatNew = flattenObject(newData || {});
        var allKeys = new Set([...Object.keys(flatOld), ...Object.keys(flatNew)]);
        var changes = { added: [], modified: [], deleted: [] };

        allKeys.forEach(function(key) {
            var oldVal = flatOld[key];
            var newVal = flatNew[key];

            if (isEmpty(oldVal) && isEmpty(newVal)) return;
            if (isEmpty(oldVal) && !isEmpty(newVal)) {
                changes.added.push({ key: key, value: newVal });
            } else if (!isEmpty(oldVal) && isEmpty(newVal)) {
                changes.deleted.push({ key: key, value: oldVal });
            } else if (!valuesEqual(oldVal, newVal)) {
                changes.modified.push({ key: key, oldValue: oldVal, newValue: newVal });
            }
        });
        return changes;
    }

    // Initialize Select2
    $("#property-select").select2({
        ajax: {
            url: singlePropSyncData.ajaxurl,
            dataType: "json",
            delay: 250,
            data: function(params) {
                return { action: "search_properties", search: params.term, page: params.page || 1, nonce: singlePropSyncData.nonce };
            },
            processResults: function(data) {
                return { results: data.results, pagination: { more: data.pagination.more } };
            },
            cache: true
        },
        placeholder: "ابحث عن العقار...",
        minimumInputLength: 0,
        language: {
            searching: function() { return "جاري البحث..."; },
            noResults: function() { return "لم يتم العثور على نتائج"; }
        },
        dir: "rtl"
    });

    // Unified sync function
    function startSync() {
        if (!selectedPropertyId) return;

        $("#auto-loading-section").removeClass("hidden");
        $("#manual-selection-section, #sync-results").addClass("hidden");
        $("#sync-single-btn").prop("disabled", true);
        updateProgress(20, "جاري تحميل بيانات العقار...");

        // Step 1: Get old REGA data
        $.ajax({
            url: singlePropSyncData.ajaxurl,
            type: "POST",
            data: { action: "get_property_details", property_id: selectedPropertyId, nonce: singlePropSyncData.nonce },
            success: function(response) {
                if (response.success) {
                    currentPropertyTitle = response.data.title;
                    oldRegaData = response.data.old_rega_data;
                    $("#auto-loading-title").text(currentPropertyTitle);
                    updateProgress(50, "جاري المزامنة مع هيئة العقار...");
                    runSync();
                } else {
                    showError("لم يتم العثور على العقار");
                }
            },
            error: function() { showError("خطأ في الاتصال"); }
        });
    }

    function runSync() {
        $.ajax({
            url: singlePropSyncData.ajaxurl,
            type: "POST",
            data: { action: "sync_single_property", property_id: selectedPropertyId, nonce: singlePropSyncData.nonce },
            success: function(response) {
                updateProgress(100, "تمت المزامنة!");
                setTimeout(function() {
                    $("#auto-loading-section").addClass("hidden");
                    $("#sync-single-btn").prop("disabled", false);
                    if (response.success) {
                        displayResults(response.data);
                    } else {
                        alert("خطأ في المزامنة");
                    }
                }, 300);
            },
            error: function() {
                showError("خطأ في المزامنة");
                $("#sync-single-btn").prop("disabled", false);
            }
        });
    }

    function updateProgress(percent, text) {
        $("#auto-loading-progress").css("width", percent + "%");
        $("#auto-loading-status").text(text);
    }

    function showError(message) {
        $("#auto-loading-section").css("background", "#fcf0f1").css("border-color", "#d63638");
        $("#auto-loading-title").html("❌ خطأ").css("color", "#d63638");
        $("#auto-loading-status").text(message).css("color", "#d63638");
    }

    // Auto-load if id in URL
    if (urlPropertyId) {
        selectedPropertyId = urlPropertyId;
        var option = new Option("جاري التحميل...", urlPropertyId, true, true);
        $("#property-select").append(option);
        startSync();
    }

    // Event handlers
    $("#property-select").on("select2:select", function(e) {
        selectedPropertyId = e.params.data.id;
        currentPropertyTitle = e.params.data.text;
        $("#sync-single-btn").prop("disabled", false);
    });

    $("#sync-single-btn, #resync-btn").on("click", function() { startSync(); });

    $("#new-property-btn").on("click", function() {
        window.location.href = window.location.pathname + "?page=single-prop-sync";
    });

    function displayResults(data) {
        $("#sync-results").removeClass("hidden");
        $("#manual-selection-section").addClass("hidden");

        var syncResult = data.sync_result;
        var newRegaData = syncResult.data;
        var statusBox = $("#sync-status-box");
        var statusMsg = $("#sync-status-message");

        if (syncResult.success) {
            statusBox.removeClass("hidden notice-error").addClass("notice notice-success");
            statusMsg.html("<strong>نجحت المزامنة!</strong> " + (syncResult.message || "") + 
                " <small>(وقت التنفيذ: " + data.execution_time + " ثانية)</small>");
        } else {
            statusBox.removeClass("hidden notice-success").addClass("notice notice-error");
            statusMsg.html("<strong>❌ فشلت المزامنة!</strong> " + (syncResult.message || ""));
        }

        $("#property-title-display").text(currentPropertyTitle);
        var metaHtml = "رقم العقار: <strong>" + selectedPropertyId + "</strong>";
        if (newRegaData && newRegaData.adLicenseNumber) {
            metaHtml += " | رقم الترخيص: <strong>" + newRegaData.adLicenseNumber + "</strong>";
        }
        metaHtml += " | <a href=\"" + singlePropSyncData.siteurl + "/?p=" + selectedPropertyId + "\" target=\"_blank\">عرض</a>";
        metaHtml += " | <a href=\"" + singlePropSyncData.adminurl + "post.php?post=" + selectedPropertyId + "&action=edit\" target=\"_blank\">تحرير</a>";
        $("#property-meta-display").html(metaHtml);

        // Compare old REGA data vs new REGA data
        displayChanges(oldRegaData, newRegaData);

        if (newRegaData) {
            $("#rega-full-json").text(JSON.stringify(newRegaData, null, 2));
        }

        $("html, body").animate({ scrollTop: $("#sync-results").offset().top - 50 }, 500);
    }

    function displayChanges(oldData, newData) {
        var changes = compareData(oldData, newData);
        var totalChanges = changes.added.length + changes.modified.length + changes.deleted.length;

        $("#changes-count").text(totalChanges);
        $("#new-values-section, #modified-values-section, #deleted-values-section, #no-changes-message").addClass("hidden");
        $("#new-values-table, #modified-values-table, #deleted-values-table").empty();

        if (totalChanges === 0) {
            $("#no-changes-message").removeClass("hidden");
            return;
        }

        if (changes.added.length > 0) {
            $("#new-values-section").removeClass("hidden");
            var html = "";
            changes.added.forEach(function(item) {
                html += "<tr class=\"value-new\"><td><strong>" + getFieldLabel(item.key) + "</strong></td>";
                html += "<td>" + formatValue(item.value) + "</td></tr>";
            });
            $("#new-values-table").html(html);
        }

        if (changes.modified.length > 0) {
            $("#modified-values-section").removeClass("hidden");
            var html = "";
            changes.modified.forEach(function(item) {
                html += "<tr><td><strong>" + getFieldLabel(item.key) + "</strong></td>";
                html += "<td class=\"value-modified-old\">" + formatValue(item.oldValue) + "</td>";
                html += "<td class=\"value-modified-new\">" + formatValue(item.newValue) + "</td></tr>";
            });
            $("#modified-values-table").html(html);
        }

        if (changes.deleted.length > 0) {
            $("#deleted-values-section").removeClass("hidden");
            var html = "";
            changes.deleted.forEach(function(item) {
                html += "<tr class=\"value-deleted\"><td><strong>" + getFieldLabel(item.key) + "</strong></td>";
                html += "<td>" + formatValue(item.value) + "</td></tr>";
            });
            $("#deleted-values-table").html(html);
        }
    }

    $("#toggle-json-btn").on("click", function() { $("#rega-full-json").toggleClass("hidden"); });
    $("#copy-json-btn").on("click", function() {
        navigator.clipboard.writeText($("#rega-full-json").text()).then(function() { alert("تم النسخ"); });
    });
});
