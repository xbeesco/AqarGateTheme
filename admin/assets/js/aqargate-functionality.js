jQuery(document).ready(function($) {
    // Populate the start file select field based on location type
    $('#location_type').on('change', function() {
        var locationType = $(this).val();
        var startFileSelect = $('#start_file');
        startFileSelect.empty();

        if (locationType === 'CITY') {
            for (var i = 1; i <= 11; i++) {
                startFileSelect.append('<option value="' + i + '">Start from File ' + i + '</option>');
            }
        } else if (locationType === 'DISTRICT') {
            for (var i = 1; i <= 3; i++) {
                startFileSelect.append('<option value="' + i + '">Start from File ' + i + '</option>');
            }
        } else {
            startFileSelect.append('<option value="1">Not Applicable</option>');
        }
    });

    // Trigger change event on page load to populate select field
    $('#location_type').trigger('change');

    // Handle form submission
    $('#aqargate-functionality-form').on('submit', function(e) {
        e.preventDefault();

        var locationType = $('select[name="location_type"]').val();
        var startFile = parseInt($('select[name="start_file"]').val());

        if (locationType === 'REGION') {
            processRegion();
        } else {
            processFiles(locationType, startFile);
        }
    });

    function updateProgressBar(processedParts, totalParts) {
        var progress = (processedParts / totalParts) * 100;
        $('#progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
    }

    function processRegion() {
        $('#loading').show();
        $('#response pre').empty();
        var data = {
            action: 'add_property_location',
            location_type: 'REGION'
        };

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            success: function(response) {
                $('#response pre').html('<p>' + response + '</p>');
                $('#loading').hide();
                $('#progress-bar').css('width', '100%').attr('aria-valuenow', 100);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#loading').hide();
                $('#response pre').html('<p>Error processing region: '- + textStatus + ' - ' + errorThrown + '</p>');
            }
        });
    }

    function processFiles(locationType, startFile) {
        var totalFiles = locationType === 'CITY' ? 11 : 3;
        var currentFile = startFile;
        var part = 1;
        var totalParts = totalFiles * 4;
        var processedParts = 0;

        $('#loading').show();
        $('#response pre').empty();

        function processNextPart() {
            if (currentFile > totalFiles) {
                $('#loading').hide();
                $('#response pre').html('<p>All ' + locationType.toLowerCase() + 's have been added successfully!</p>');
                return;
            }

            var data = {
                action: 'add_property_location',
                location_type: locationType,
                file_number: currentFile,
                part: part
            };

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#response pre').html('<p>' + response + '</p><p>Processed file ' + currentFile + ' part ' + part + ' for ' + locationType.toLowerCase() + 's.</p>');
                    processedParts++;
                    updateProgressBar(processedParts, totalParts);
                    if (part === 4) {
                        part = 1;
                        currentFile++;
                    } else {
                        part++;
                    }
                    processNextPart();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loading').hide();
                    $('#response pre').html('<p>Error processing file ' + currentFile + ' part ' + part + ': ' + textStatus + ' - ' + errorThrown + '</p>');
                }
            });
        }

        processNextPart();
    }

    $('#sync-locations-button').on('click', function() {
        $('#sync-locations-progress').show();
        $('#sync-locations-status').html('');
        $('#sync-locations-log').html('');
        $('#sync-locations-button').prop('disabled', true);

        $.ajax({
            url: aqargateSyncLocations.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'sync_locations',
                nonce: aqargateSyncLocations.nonce
            },
            success: function(response) {
                if (response && response.progress) {
                    $('#sync-locations-progress-bar').css('width', response.progress + '%').attr('aria-valuenow', response.progress);
                    $('#sync-locations-status').html(response.message);
                    $('#sync-locations-log').html(buildTable(response.log));
                    $('#sync-locations-button').prop('disabled', false);
                } else {
                    $('#sync-locations-status').html('An error occurred during the synchronization process.');
                    $('#sync-locations-button').prop('disabled', false);
                }
            },
            error: function() {
                $('#sync-locations-status').html('An error occurred during the synchronization process.');
                $('#sync-locations-button').prop('disabled', false);
            }
        });
    });

    $('#sync-properties-button').on('click', function() {
        $('#sync-properties-progress').show();
        $('#sync-properties-status').html('');
        $('#sync-properties-log').html('');
        $('#sync-properties-button').prop('disabled', true);
    
        function syncBatch(offset) {
            $.ajax({
                url: aqargateSyncLocations.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'sync_properties',
                    nonce: aqargateSyncLocations.nonce,
                    offset: offset
                },
                success: function(response) {
                    if (response && response.progress !== undefined) {
                        $('#sync-properties-progress-bar').css('width', response.progress + '%').attr('aria-valuenow', response.progress);
                        $('#sync-properties-status').html(response.message);
                        $('#sync-properties-log').append(buildSyncTable(response.log));
    
                        if (response.next_offset !== null) {
                            syncBatch(response.next_offset);  // Recursive call to process the next batch
                        } else {
                            $('#sync-properties-button').prop('disabled', false);
                        }
                    } else {
                        $('#sync-properties-status').html('An error occurred during the synchronization process.');
                        $('#sync-properties-button').prop('disabled', false);
                    }
                },
                error: function() {
                    $('#sync-properties-status').html('An error occurred during the synchronization process.');
                    $('#sync-properties-button').prop('disabled', false);
                }
            });
        }
    
        syncBatch(0);  // Start with the first batch
    });

    $('#sync-expired-properties-button').on('click', function() {
        $('#sync-properties-progress').show();
        $('#sync-properties-status').html('');
        $('#sync-properties-log').html('');
        $('#sync-expired-properties-button').prop('disabled', true);
    
        function syncBatch(offset) {
            $.ajax({
                url: aqargateSyncLocations.ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'sync_expire_properties',
                    nonce: aqargateSyncLocations.nonce,
                    offset: offset
                },
                success: function(response) {
                    if (response && response.progress !== undefined) {
                        $('#sync-properties-progress-bar').css('width', response.progress + '%').attr('aria-valuenow', response.progress);
                        $('#sync-properties-status').html(response.message);
                        $('#sync-properties-log').append(buildSyncTable(response.log));
    
                        if (response.next_offset !== null) {
                            syncBatch(response.next_offset);  // Recursive call to process the next batch
                        } else {
                            $('#sync-expired-properties-button').prop('disabled', false);
                        }
                    } else {
                        $('#sync-properties-status').html('An error occurred during the synchronization process.');
                        $('#sync-expired-properties-button').prop('disabled', false);
                    }
                },
                error: function() {
                    $('#sync-properties-status').html('An error occurred during the synchronization process.');
                    $('#sync-expired-properties-button').prop('disabled', false);
                }
            });
        }
    
        syncBatch(0);  // Start with the first batch
    });
    

    function buildSyncTable(logEntries) {
        console.log(logEntries);
        var table = '<table class="table table-striped table-bordered">';
        table += '<thead><tr><th>ID</th><th>Property</th><th>Status</th><th>Message</th></tr></thead>';
        table += '<tbody>';

        logEntries.forEach(function(entry) {
            table += '<tr>';
            table += '<td>' + entry.ID + '</td>';
            table += '<td>' + entry.Property + '</td>';
            table += '<td>' + entry.Status + '</td>';
            table += '<td>' + entry.Message + '</td>';
            table += '</tr>';
        });

        table += '</tbody></table>';
        return table;
    }

    function buildTable(logEntries) {
        console.log(logEntries);
        var table = '<table class="table table-striped table-bordered">';
        table += '<thead><tr><th>ID</th><th>Property</th><th>State</th><th>City</th><th>Area</th></tr></thead>';
        table += '<tbody>';

        logEntries.forEach(function(entry) {
            table += '<tr>';
            table += '<td>' + entry.ID + '</td>';
            table += '<td>' + entry.Property + '</td>';
            table += '<td>' + entry.State + '</td>';
            table += '<td>' + entry.City + '</td>';
            table += '<td>' + entry.Area + '</td>';
            table += '</tr>';
        });

        table += '</tbody></table>';
        return table;
    }

    $('#fetch-agency-users').on('click', function() {
        $('#fetch-agency-users').prop('disabled', true);
        $('#agency-users-table').hide();
        $('#agency-users-body').empty();
        $('#loading').show();

        $.ajax({
            url: aqargateSyncLocations.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'fetch_agency_users',
                nonce: aqargateSyncLocations.nonce
            },
            success: function(response) {
                $('#loading').hide();
                if (response && response.users) {
                    $.each(response.users, function(index, user) {
                        $('#agency-users-body').append(
                            '<tr>' +
                            '<td>' + user.ID + '</td>' +
                            '<td>' + user.display_name + '</td>' +
                            '<td>' + user.first_name + '</td>' +
                            '<td>' + user.last_name + '</td>' +
                            '<td>' + user.role + '</td>' +
                            '</tr>'
                        );
                    });
                    $('#agency-users-table').show();
                } else {
                    alert('No users found or an error occurred.');
                }
                $('#fetch-agency-users').prop('disabled', false);
            },
            error: function() {
                $('#loading').hide();
                alert('An error occurred during the AJAX request.');
                $('#fetch-agency-users').prop('disabled', false);
            }
        });
    });

    
});
