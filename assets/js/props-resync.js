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

    // Start sync button click
    $('#start-resync-btn').on('click', function() {
        if (syncInProgress) return;

        // Get form values
        batchSize = parseInt($('#batch-size').val()) || 20;
        propertyFilter = $('#property-filter').val() || 'published';

        // Reset counters
        syncedCount = 0;
        failedCount = 0;
        currentOffset = 0;
        syncStopped = false;
        startTime = new Date();
        logCounter = 0;

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

        updateStatus('تم إيقاف المزامنة بواسطة المستخدم');
    });

    // Clear log button
    $('#clear-log-btn').on('click', function() {
        logCounter = 0;
        $('#resync-log-tbody').html('<tr><td colspan="6" style="text-align: center; padding: 40px; color: #646970;">لا توجد عمليات بعد</td></tr>');
    });

    // Export log button
    $('#export-log-btn').on('click', function() {
        exportLogToCSV();
    });

    // Get total count of properties
    function getTotalCount() {
        $.ajax({
            url: propsResyncData.ajaxurl,
            type: 'POST',
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
                    alert('حدث خطأ في جلب عدد العقارات');
                }
            },
            error: function() {
                alert('حدث خطأ في الاتصال');
            }
        });
    }

    // Start the sync process
    function startSync() {
        syncInProgress = true;

        $('#start-resync-btn').prop('disabled', true).hide();
        $('#stop-resync-btn').show();
        $('.props-resync-form input, .props-resync-form select').prop('disabled', true);

        updateStatus('جاري المزامنة...');

        processNextBatch();
    }

    // Process next batch of properties
    function processNextBatch() {
        if (syncStopped) {
            return;
        }

        if (currentOffset >= totalProperties) {
            completeSymc();
            return;
        }

        updateStatus('جاري معالجة العقارات من ' + (currentOffset + 1) + ' إلى ' + Math.min(currentOffset + batchSize, totalProperties));

        $.ajax({
            url: propsResyncData.ajaxurl,
            type: 'POST',
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
                            addLogEntry(result);

                            if (result.success) {
                                syncedCount++;
                            } else {
                                failedCount++;
                            }
                        });
                    }

                    // Update counters
                    $('#synced-count').text(syncedCount);
                    $('#failed-count').text(failedCount);

                    // Update progress bar
                    let progress = Math.round((syncedCount + failedCount) / totalProperties * 100);
                    updateProgressBar(progress);

                    // Calculate estimated time
                    updateEstimatedTime();

                    // Update offset
                    currentOffset = response.data.offset;

                    // Process next batch or complete
                    if (response.data.completed) {
                        completeSync();
                    } else {
                        // Continue with next batch
                        setTimeout(processNextBatch, 500);
                    }
                } else {
                    updateStatus('حدث خطأ: ' + (response.data.message || 'خطأ غير معروف'));
                    stopSync();
                }
            },
            error: function(xhr, status, error) {
                updateStatus('حدث خطأ في الاتصال: ' + error);
                stopSync();
            }
        });
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
        if (!startTime || syncedCount === 0) {
            $('#estimated-time').text('--:--');
            return;
        }

        let elapsed = (new Date() - startTime) / 1000; // seconds
        let avgTimePerProperty = elapsed / (syncedCount + failedCount);
        let remaining = (totalProperties - syncedCount - failedCount) * avgTimePerProperty;

        let minutes = Math.floor(remaining / 60);
        let seconds = Math.floor(remaining % 60);

        $('#estimated-time').text(
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0')
        );
    }

    // Add entry to log table
    function addLogEntry(result) {
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

        let row = '<tr>' +
            '<td>' + logCounter + '</td>' +
            '<td>' + result.id + '</td>' +
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
        $('.props-resync-form input, .props-resync-form select').prop('disabled', false);

        $('#estimated-time').text('00:00');

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
        $('.props-resync-form input, .props-resync-form select').prop('disabled', false);
    }

    // Export log to CSV
    function exportLogToCSV() {
        let csvContent = 'data:text/csv;charset=utf-8,';
        csvContent += '#,ID,العنوان,الحالة,الرسالة,الوقت\n';

        $('#resync-log-tbody tr').each(function() {
            let row = [];
            $(this).find('td').each(function(index) {
                if (index === 2) {
                    // Get text from link
                    row.push('"' + $(this).find('a').text().replace(/"/g, '""') + '"');
                } else if (index === 3) {
                    // Get text from status badge
                    row.push('"' + $(this).find('.status-badge').text().replace(/"/g, '""') + '"');
                } else {
                    row.push('"' + $(this).text().replace(/"/g, '""') + '"');
                }
            });
            csvContent += row.join(',') + '\n';
        });

        let encodedUri = encodeURI(csvContent);
        let link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'props-resync-log-' + Date.now() + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
});
