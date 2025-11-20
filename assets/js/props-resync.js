jQuery(document).ready(function($) {
    let syncInProgress = false;
    let syncStopped = false;
    let totalProperties = 0;
    let syncedCount = 0;
    let failedCount = 0;
    let currentOffset = 0;
    let batchSize = 20;
    let propertyFilter = 'published';
    let startTime = null;
    let logCounter = 0;
    let heartbeatInterval = null;
    let maxRetries = 3;
    let retryDelay = 2000; // 2 seconds
    let processedPropertyIds = new Set(); // Track processed property IDs to prevent duplicates

    // Start sync button click
    $('#start-resync-btn').on('click', function() {
        if (syncInProgress) return;

        // Get form values
        batchSize = parseInt($('#batch-size').val()) || 20;
        propertyFilter = $('#property-filter').val() || 'published';

        // Reset counters and tracking
        syncedCount = 0;
        failedCount = 0;
        currentOffset = 0;
        syncStopped = false;
        startTime = new Date();
        logCounter = 0;
        processedPropertyIds.clear(); // Clear tracked IDs

        // Clear log
        $('#resync-log-tbody').empty();

        // Get total count first
        getTotalCount();
    });

    // Stop sync button click
    $('#stop-resync-btn').on('click', function() {
        syncStopped = true;
        syncInProgress = false;

        $('#start-resync-btn').prop('disabled', false).show();
        $('#stop-resync-btn').hide();
        $('#resume-resync-btn').hide();

        // Stop heartbeat
        stopHeartbeat();

        updateStatus('تم إيقاف المزامنة بواسطة المستخدم');
    });

    // Resume sync button click
    $('#resume-resync-btn').on('click', function() {
        if (syncInProgress) return;

        // Don't reset counters or processedPropertyIds, continue from where we stopped
        syncStopped = false;

        // Hide resume button, show stop button
        $('#resume-resync-btn').hide();
        $('#stop-resync-btn').show();
        $('#start-resync-btn').hide();

        updateStatus('جاري استكمال المزامنة...');

        // Restart sync from current offset
        syncInProgress = true;

        // Restart heartbeat
        startHeartbeat();

        // Continue processing
        processNextBatch(0);
    });

    // Get total count of properties
    function getTotalCount() {
        $.ajax({
            url: propsResyncData.ajaxurl,
            type: 'POST',
            timeout: 60000, // 1 minute timeout
            data: {
                action: 'get_props_sync_count',
                filter: propertyFilter,
                nonce: propsResyncData.nonce
            },
            success: function(response) {
                if (response.success) {
                    totalProperties = response.data.total;

                    if (totalProperties === 0) {
                        alert('لا توجد عقارات للمزامنة');
                        return;
                    }

                    $('#total-count').text(totalProperties);

                    // Show progress section
                    $('#resync-progress-section').slideDown();
                    $('#resync-log-section').slideDown();

                    // Start syncing
                    startSync();
                } else {
                    alert('حدث خطأ في جلب عدد العقارات: ' + (response.data.message || 'خطأ غير معروف'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error getting count:', status, error);
                alert('حدث خطأ في الاتصال: ' + error);
            }
        });
    }

    // Start the sync process
    function startSync() {
        syncInProgress = true;

        $('#start-resync-btn').prop('disabled', true).hide();
        $('#stop-resync-btn').show();
        $('#resume-resync-btn').hide();
        $('.props-resync-form input, .props-resync-form select').prop('disabled', true);

        updateStatus('جاري المزامنة...');

        // Start heartbeat
        startHeartbeat();

        processNextBatch();
    }

    // Process next batch of properties (with retry mechanism)
    function processNextBatch(retryCount = 0) {
        if (syncStopped) {
            return;
        }

        if (currentOffset >= totalProperties) {
            completeSync();
            return;
        }

        updateStatus('جاري معالجة العقارات من ' + (currentOffset + 1) + ' إلى ' + Math.min(currentOffset + batchSize, totalProperties));

        $.ajax({
            url: propsResyncData.ajaxurl,
            type: 'POST',
            timeout: 600000, // 10 minutes timeout (increased)
            data: {
                action: 'process_bulk_props_sync',
                batch_size: batchSize,
                offset: currentOffset,
                filter: propertyFilter,
                nonce: propsResyncData.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Process results
                    if (response.data.results && response.data.results.length > 0) {
                        response.data.results.forEach(function(result) {
                            // Check if this property was already processed
                            let propertyId = result.id;
                            let isNewProperty = !processedPropertyIds.has(propertyId);

                            // Always add to log for transparency
                            addLogEntry(result, isNewProperty);

                            // Only count if it's a new property
                            if (isNewProperty) {
                                processedPropertyIds.add(propertyId);

                                if (result.success) {
                                    syncedCount++;
                                } else {
                                    failedCount++;
                                }
                            } else {
                                // Log duplicate detection
                                console.warn('Duplicate property detected:', propertyId, '- Not counting again');
                            }
                        });
                    }

                    // Update counters
                    $('#synced-count').text(syncedCount);
                    $('#failed-count').text(failedCount);

                    // Update progress bar (based on actual unique processed count)
                    let actualProcessed = syncedCount + failedCount;
                    let progress = Math.min(Math.round((actualProcessed / totalProperties) * 100), 100);
                    updateProgressBar(progress);

                    // Calculate estimated time
                    updateEstimatedTime();

                    // Update offset
                    currentOffset = response.data.offset;

                    // Reset retry count on success
                    retryCount = 0;

                    // Process next batch or complete
                    if (response.data.completed || actualProcessed >= totalProperties) {
                        completeSync();
                    } else {
                        // Continue with next batch after a short delay
                        setTimeout(function() {
                            processNextBatch(0);
                        }, 500);
                    }
                } else {
                    let errorMsg = response.data.message || 'خطأ غير معروف';
                    console.error('Batch processing error:', errorMsg);

                    // Try to retry
                    if (retryCount < maxRetries) {
                        let nextRetry = retryCount + 1;
                        updateStatus('حدث خطأ، جاري المحاولة مرة أخرى (' + nextRetry + '/' + maxRetries + ')...');

                        setTimeout(function() {
                            processNextBatch(nextRetry);
                        }, retryDelay * (retryCount + 1)); // Exponential backoff
                    } else {
                        // Show resume button instead of stopping completely
                        let resumeMsg = 'حدث خطأ: ' + errorMsg + '<br><strong style="color: #d63638;">تم إيقاف المزامنة مؤقتاً.</strong> يمكنك الضغط على "استكمال المزامنة" للمتابعة من العقار رقم ' + (currentOffset + 1);
                        updateStatus(resumeMsg);
                        pauseSyncWithResume();
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error, xhr);

                let errorMessage = 'حدث خطأ في الاتصال: ' + error;

                // Check for specific errors
                if (status === 'timeout') {
                    errorMessage = 'انتهت مهلة الاتصال. قد تكون عملية المزامنة بطيئة جداً.';
                } else if (xhr.status === 500) {
                    errorMessage = 'خطأ في الخادم (500). الرجاء التحقق من سجلات الأخطاء.';
                } else if (xhr.status === 0) {
                    errorMessage = 'لا يوجد اتصال بالخادم. الرجاء التحقق من الاتصال بالإنترنت.';
                }

                // Try to retry
                if (retryCount < maxRetries) {
                    let nextRetry = retryCount + 1;
                    updateStatus('حدث خطأ في الاتصال، جاري المحاولة مرة أخرى (' + nextRetry + '/' + maxRetries + ')...');

                    setTimeout(function() {
                        processNextBatch(nextRetry);
                    }, retryDelay * (retryCount + 1)); // Exponential backoff
                } else {
                    // Show resume button instead of stopping completely
                    let resumeMsg = errorMessage + '<br><strong style="color: #d63638;">تم إيقاف المزامنة مؤقتاً.</strong> يمكنك الضغط على "استكمال المزامنة" للمتابعة من العقار رقم ' + (currentOffset + 1);
                    updateStatus(resumeMsg);
                    pauseSyncWithResume();
                }
            }
        });
    }

    // Start heartbeat to keep session alive
    function startHeartbeat() {
        heartbeatInterval = setInterval(function() {
            $.ajax({
                url: propsResyncData.ajaxurl,
                type: 'POST',
                timeout: 10000,
                data: {
                    action: 'props_resync_heartbeat',
                    nonce: propsResyncData.nonce
                },
                success: function(response) {
                    console.log('Heartbeat:', response.data.timestamp);
                },
                error: function() {
                    console.warn('Heartbeat failed');
                }
            });
        }, 30000); // Every 30 seconds
    }

    // Stop heartbeat
    function stopHeartbeat() {
        if (heartbeatInterval) {
            clearInterval(heartbeatInterval);
            heartbeatInterval = null;
        }
    }

    // Pause sync and show resume button
    function pauseSyncWithResume() {
        syncInProgress = false;

        // Hide stop button, show resume button
        $('#stop-resync-btn').hide();
        $('#resume-resync-btn').show();
        $('#start-resync-btn').hide();

        // Keep form disabled
        $('.props-resync-form input, .props-resync-form select').prop('disabled', true);

        // Stop heartbeat
        stopHeartbeat();
    }

    // Update progress bar
    function updateProgressBar(percentage) {
        $('#resync-progress-bar').css('width', percentage + '%');
        $('#resync-progress-text').text(percentage + '%');
    }

    // Update status message
    function updateStatus(message) {
        $('#resync-status').html('<strong>الحالة:</strong> ' + message);
    }

    // Update estimated time remaining
    function updateEstimatedTime() {
        if (!startTime || (syncedCount + failedCount) === 0) {
            $('#estimated-time').text('--:--');
            return;
        }

        let elapsed = (new Date() - startTime) / 1000; // seconds
        let actualProcessed = syncedCount + failedCount;
        let avgTimePerProperty = elapsed / actualProcessed;
        let remaining = (totalProperties - actualProcessed) * avgTimePerProperty;

        // Ensure non-negative
        if (remaining < 0) remaining = 0;

        let minutes = Math.floor(remaining / 60);
        let seconds = Math.floor(remaining % 60);

        $('#estimated-time').text(
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0')
        );
    }

    // Add entry to log table
    function addLogEntry(result, isNewProperty) {
        logCounter++;

        // Remove "no data" row if it exists
        if (logCounter === 1) {
            $('#resync-log-tbody').empty();
        }

        let statusClass = result.success ? 'status-success' : 'status-error';
        let statusText = result.success ? 'نجح' : 'فشل';

        if (result.expired) {
            statusText = 'منتهي';
            statusClass = 'status-error';
        }

        // Mark duplicates
        let duplicateMarker = '';
        if (!isNewProperty) {
            duplicateMarker = ' <span style="color: #ff6b00; font-size: 11px;">(مكرر)</span>';
            statusClass = 'status-duplicate';
        }

        let row = '<tr>' +
            '<td>' + logCounter + '</td>' +
            '<td>' + result.id + duplicateMarker + '</td>' +
            '<td><a href="' + result.url + '" target="_blank" class="property-link">' + result.title + '</a></td>' +
            '<td><span class="status-badge ' + statusClass + '">' + statusText + '</span></td>' +
            '<td>' + result.message + '</td>' +
            '<td>' + result.time + '</td>' +
            '</tr>';

        $('#resync-log-tbody').prepend(row);

        // Scroll to top of log
        $('#resync-log-container').scrollTop(0);
    }

    // Complete sync process
    function completeSync() {
        syncInProgress = false;

        updateProgressBar(100);
        updateStatus('تمت المزامنة بنجاح! العقارات المتزامنة: ' + syncedCount + ' | الفاشلة: ' + failedCount);

        $('#start-resync-btn').prop('disabled', false).show();
        $('#stop-resync-btn').hide();
        $('#resume-resync-btn').hide();
        $('.props-resync-form input, .props-resync-form select').prop('disabled', false);

        $('#estimated-time').text('00:00');

        // Stop heartbeat
        stopHeartbeat();

        // Show completion notification
        if (typeof adminNotice !== 'undefined') {
            adminNotice('تمت عملية المزامنة بنجاح!', 'success');
        }
    }

    // Stop sync process
    function stopSync() {
        syncInProgress = false;

        $('#start-resync-btn').prop('disabled', false).show();
        $('#stop-resync-btn').hide();
        $('#resume-resync-btn').hide();
        $('.props-resync-form input, .props-resync-form select').prop('disabled', false);

        // Stop heartbeat
        stopHeartbeat();
    }
});
