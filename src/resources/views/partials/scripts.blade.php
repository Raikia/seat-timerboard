<script>
    $(document).ready(function() {
        var structureTypes = @json($structureTypes);
        var availableTags = @json($timerTagOptions);
        var availableRoles = @json($timerRoleOptions);
        var defaultRoleId = @json($defaultRoleId);
        var batchOldTimers = @json($batchOldTimers);
        var batchHadErrors = @json($batchHadErrors);
        var editHadErrors = @json($editHadErrors);
        var oldEditValues = @json($oldEditValues);
        var batchRowCounter = 0;
        var activeSelect2Instance = null;
        var noteModalApplyHandler = null;

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function normalizeNotes(value) {
            return $.trim(value || '');
        }

        function hasNotes(value) {
            return normalizeNotes(value).length > 0;
        }

        function renderNotesHtml(value) {
            return escapeHtml(normalizeNotes(value)).replace(/\n/g, '<br>');
        }

        function buildNoteTitle(structureName, system) {
            if (normalizeNotes(structureName)) {
                return structureName;
            }

            if (normalizeNotes(system)) {
                return system;
            }

            return 'New timer';
        }

        function updateTimerNoteCounter(value) {
            var count = (value || '').length;
            $('#timer-note-char-count').text(count + (count === 1 ? ' character' : ' characters'));
        }

        function updateNoteLaunchButton($button, notes) {
            var noteExists = hasNotes(notes);

            $button.toggleClass('btn-outline-secondary', !noteExists);
            $button.toggleClass('btn-outline-info', noteExists);
            $button.toggleClass('has-note', noteExists);
            $button.find('.note-btn-label').text(noteExists ? 'Edit note' : 'Add note');
        }

        function updateBatchRowNoteButton($row) {
            updateNoteLaunchButton($row.find('.batch-note-btn'), $row.find('.batch-note-input').val());
        }

        function updateEditNoteButton() {
            var $button = $('#edit-note-btn');
            var noteExists = hasNotes($('#edit_notes').val());

            $button.toggleClass('has-note', noteExists);
            $button.find('.note-btn-label').text(noteExists ? 'Edit saved note' : 'Add optional note');
        }

        function openTimerNoteModal(config) {
            noteModalApplyHandler = typeof config.onApply === 'function' ? config.onApply : null;

            var notes = config.notes || '';
            var editable = !!config.editable;

            $('#timerNoteModalTitle').text(config.title || 'Timer note');
            $('#timerNoteModalSubtitle').text(config.subtitle || '');
            $('#timer_notes').val(notes);
            updateTimerNoteCounter(notes);

            $('#timerNoteEditor').toggleClass('d-none', !editable);
            $('#timerNoteReadonly').toggleClass('d-none', editable);
            $('#clearTimerNoteBtn').toggleClass('d-none', !editable);
            $('#applyTimerNoteDraftBtn').toggleClass('d-none', !editable);

            if (editable) {
                $('#timerNoteReadonly').empty();
            } else if (hasNotes(notes)) {
                $('#timerNoteReadonly').html(renderNotesHtml(notes));
            } else {
                $('#timerNoteReadonly').html('<span class="text-muted">No note saved for this timer yet.</span>');
            }

            $('#timerNoteModal').modal('show');
        }

        $('#timerNoteModal').on('show.bs.modal', function() {
            setTimeout(function() {
                $('.modal-backdrop').last().addClass('timer-note-backdrop');
            }, 0);
        });

        $('#timerNoteModal').on('hidden.bs.modal', function() {
            $('.modal-backdrop.timer-note-backdrop').removeClass('timer-note-backdrop');

            if ($('.modal.show').length) {
                $('body').addClass('modal-open');
            }
        });

        function abortSelectRequest($element) {
            var activeRequest = $element.data('select2ActiveRequest');

            if (activeRequest && activeRequest.readyState !== 4) {
                activeRequest.abort();
            }

            $element.removeData('select2ActiveRequest');
        }

        function buildAjaxConfig($element, url, placeholder, allowClear) {
            return {
                theme: 'bootstrap4',
                placeholder: placeholder,
                minimumInputLength: 3,
                allowClear: !!allowClear,
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 150,
                    data: function (params) {
                        return { q: params.term };
                    },
                    transport: function (params, success, failure) {
                        abortSelectRequest($element);

                        var request = $.ajax(params);

                        $element.data('select2ActiveRequest', request);

                        request.then(success);
                        request.fail(function(jqXHR, textStatus, errorThrown) {
                            if (textStatus === 'abort') {
                                return;
                            }

                            failure(jqXHR, textStatus, errorThrown);
                        });
                        request.always(function() {
                            if ($element.data('select2ActiveRequest') === request) {
                                $element.removeData('select2ActiveRequest');
                            }
                        });

                        return request;
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            };
        }

        function initStructureTypeSelect($elements, $fallbackParent) {
            $elements.each(function() {
                var $element = $(this);

                $element.select2({
                    theme: 'bootstrap4',
                    dropdownParent: $fallbackParent,
                    placeholder: 'Select Structure Type',
                    allowClear: true,
                    width: '100%'
                });
            });
        }

        function initRemoteSelect($elements, $fallbackParent, url, placeholder, allowClear) {
            $elements.each(function() {
                var $element = $(this);

                $element.select2($.extend({}, buildAjaxConfig($element, url, placeholder, allowClear), {
                    dropdownParent: $fallbackParent,
                    width: '100%'
                }));

                $element.on('select2:close.select2Abort select2:closing.select2Abort', function() {
                    abortSelectRequest($element);
                });
            });
        }

        function repositionSelect2Dropdown(instance) {
            if (!instance || !instance.$dropdown || !instance.$container) {
                return;
            }

            var dropdownParent = instance.options.get('dropdownParent');
            var $dropdownParent = dropdownParent && dropdownParent.jquery ? dropdownParent : $(dropdownParent);

            if (!$dropdownParent.length) {
                return;
            }

            var parentOffset = $dropdownParent.offset();
            var containerOffset = instance.$container.offset();

            if (!parentOffset || !containerOffset) {
                return;
            }

            instance.$dropdown.css({
                top: containerOffset.top - parentOffset.top + instance.$container.outerHeight(false),
                left: containerOffset.left - parentOffset.left,
                width: instance.$container.outerWidth(false)
            });
        }

        function setSelectValue($select, value) {
            if (value === null || value === undefined || value === '') {
                $select.val(null).trigger('change');
                return;
            }

            var hasOption = false;
            $select.find('option').each(function() {
                if ($(this).val() == value) {
                    hasOption = true;
                }
            });

            if (!hasOption) {
                $select.append(new Option(value, value, true, true));
            }

            $select.val(value).trigger('change');
        }

        function formatUtcTimestamp(timeString) {
            var date = new Date(timeString);

            return date.getUTCFullYear() + '.' +
                ('0' + (date.getUTCMonth() + 1)).slice(-2) + '.' +
                ('0' + date.getUTCDate()).slice(-2) + ' ' +
                ('0' + date.getUTCHours()).slice(-2) + ':' +
                ('0' + date.getUTCMinutes()).slice(-2) + ':' +
                ('0' + date.getUTCSeconds()).slice(-2);
        }

        function buildStructureTypeOptions(selectedValue) {
            var options = '<option value="">Select Type</option>';

            $.each(structureTypes, function(value, label) {
                var selected = String(selectedValue || '') === String(value) ? ' selected' : '';
                options += '<option value="' + escapeHtml(value) + '"' + selected + '>' + escapeHtml(label) + '</option>';
            });

            return options;
        }

        function buildRoleOptions(selectedValue) {
            var normalizedValue = selectedValue === null || selectedValue === undefined ? '' : String(selectedValue);
            var options = '<option value="">Public (Everyone)</option>';

            availableRoles.forEach(function(role) {
                var selected = normalizedValue === String(role.id) ? ' selected' : '';
                options += '<option value="' + role.id + '"' + selected + '>' + escapeHtml(role.title) + '</option>';
            });

            return options;
        }

        function buildTagMarkup(rowKey, selectedTags) {
            var normalizedTags = (selectedTags || []).map(function(tagId) {
                return String(tagId);
            });

            return availableTags.map(function(tag) {
                var checkboxId = 'batch_tag_' + rowKey + '_' + tag.id;
                var checked = normalizedTags.indexOf(String(tag.id)) !== -1 ? ' checked' : '';

                return '' +
                    '<div class="m-1">' +
                        '<input type="checkbox" name="timers[' + rowKey + '][tags][]" value="' + tag.id + '" id="' + checkboxId + '" class="d-none tag-checkbox"' + checked + '>' +
                        '<label class="badge p-2 tag-badge" for="' + checkboxId + '" style="background-color: ' + escapeHtml(tag.color) + '; color: #fff; cursor: pointer; opacity: 0.5; border: 2px solid transparent;" data-color="' + escapeHtml(tag.color) + '">' + escapeHtml(tag.name) + '</label>' +
                    '</div>';
            }).join('');
        }

        function buildBatchRow(rowKey, timerData) {
            var data = timerData || {};
            var selectedRole = data.role_id !== undefined && data.role_id !== null && data.role_id !== ''
                ? data.role_id
                : (defaultRoleId || '');
            var template = $('#batch-timer-row-template').html();

            return template
                .replace(/__ROW_KEY__/g, String(rowKey))
                .replace('__STRUCTURE_TYPE_OPTIONS__', buildStructureTypeOptions(data.structure_type))
                .replace('__STRUCTURE_NAME__', escapeHtml(data.structure_name))
                .replace('__NOTES__', escapeHtml(data.notes))
                .replace('__TIME_INPUT__', escapeHtml(data.time_input))
                .replace('__TAG_MARKUP__', buildTagMarkup(rowKey, data.tags || []))
                .replace('__ROLE_OPTIONS__', buildRoleOptions(selectedRole));
        }

        function refreshBatchRowTitles() {
            var rowCount = $('#batch-timer-rows .batch-timer-row').length;

            $('#batch-timer-rows .batch-timer-row').each(function(index) {
                $(this).find('.batch-row-index').text(index + 1);
                $(this).find('.batch-row-title').text('Timer ' + (index + 1));
            });

            var disableRemove = rowCount === 1;
            $('.remove-batch-row-btn').prop('disabled', disableRemove);

            var countLabel = rowCount === 1 ? '1 timer' : rowCount + ' timers';
            $('#batch-timer-count').text(countLabel);
            $('#batch-footer-summary').text(rowCount === 1 ? '1 timer ready to save' : rowCount + ' timers ready to save');
        }

        function roleTitleForValue(roleId) {
            if (roleId === null || roleId === undefined || roleId === '') {
                return 'Public';
            }

            var normalizedRoleId = String(roleId);
            var matchedRole = availableRoles.find(function(role) {
                return String(role.id) === normalizedRoleId;
            });

            return matchedRole ? matchedRole.title : 'Restricted';
        }

        function createSummaryPill(iconClass, text, isPlaceholder) {
            var pillClass = 'batch-summary-pill' + (isPlaceholder ? ' is-placeholder' : '');

            return '<span class="' + pillClass + '"><i class="' + iconClass + '"></i>' + escapeHtml(text) + '</span>';
        }

        function updateBatchRowSummary($row) {
            var data = collectBatchRowData($row);
            var summaryBits = [];

            summaryBits.push(createSummaryPill('fas fa-map-marker-alt', data.system || 'System pending', !data.system));
            summaryBits.push(createSummaryPill('fas fa-building', data.structure_type || 'Type pending', !data.structure_type));
            summaryBits.push(createSummaryPill('far fa-clock', data.time_input || 'Time pending', !data.time_input));
            summaryBits.push(createSummaryPill('fas fa-flag', data.owner_corporation || 'Owner pending', !data.owner_corporation));

            if (data.structure_name) {
                summaryBits.push(createSummaryPill('fas fa-signature', data.structure_name, false));
            }

            if (data.attacker_corporation) {
                summaryBits.push(createSummaryPill('fas fa-crosshairs', data.attacker_corporation, false));
            }

            if (data.tags.length) {
                summaryBits.push(createSummaryPill('fas fa-tags', data.tags.length + (data.tags.length === 1 ? ' tag' : ' tags'), false));
            }

            summaryBits.push(createSummaryPill('fas fa-user-shield', roleTitleForValue(data.role_id), false));

            $row.find('.batch-row-summary').html(summaryBits.join(''));
        }

        function setBatchRowExpanded($row, expanded, immediate) {
            var $body = $row.find('.card-body');
            var $heading = $row.find('.batch-row-heading');
            var $toggle = $row.find('.toggle-batch-row-btn');

            $row.toggleClass('is-collapsed', !expanded);
            $row.toggleClass('is-active', expanded);
            $heading.attr('aria-expanded', expanded ? 'true' : 'false');
            $toggle.attr('aria-expanded', expanded ? 'true' : 'false');

            if (immediate) {
                $body.toggle(expanded);
            } else {
                $body.stop(true, true)[expanded ? 'slideDown' : 'slideUp'](140);
            }
        }

        function activateBatchRow($row, immediate) {
            $('#batch-timer-rows .batch-timer-row').not($row).each(function() {
                setBatchRowExpanded($(this), false, immediate);
            });

            setBatchRowExpanded($row, true, immediate);
            updateBatchRowSummary($row);
        }

        function initializeBatchRow($row, timerData) {
            var data = timerData || {};
            var $modal = $('#batchTimerModal');

            initStructureTypeSelect($row.find('.batch-structure-type-select'), $modal);
            initRemoteSelect($row.find('.batch-system-select'), $modal, '{{ route("timerboard.search.systems") }}', 'Search for a system or celestial...', false);
            initRemoteSelect($row.find('.batch-owner-corporation-select'), $modal, '{{ route("timerboard.search.corporations") }}', 'Search for corporation or alliance...', false);
            initRemoteSelect($row.find('.batch-attacker-corporation-select'), $modal, '{{ route("timerboard.search.corporations") }}', 'Search for attacker (corp/alliance)...', true);

            setSelectValue($row.find('.batch-system-select'), data.system || '');
            $row.find('.batch-structure-type-select').val(data.structure_type || null).trigger('change');
            setSelectValue($row.find('.batch-owner-corporation-select'), data.owner_corporation || '');
            setSelectValue($row.find('.batch-attacker-corporation-select'), data.attacker_corporation || '');
            $row.find('.batch-role-select').val(data.role_id !== undefined && data.role_id !== null ? data.role_id : (defaultRoleId || '')).trigger('change');
            $row.find('.tag-checkbox').trigger('change');
            updateBatchRowNoteButton($row);
            updateBatchRowSummary($row);
        }

        function collectBatchRowData($row) {
            var tags = [];

            $row.find('.tag-checkbox:checked').each(function() {
                tags.push($(this).val());
            });

            return {
                system: $row.find('.batch-system-select').val() || '',
                structure_type: $row.find('.batch-structure-type-select').val() || '',
                structure_name: $row.find('input[name$="[structure_name]"]').val() || '',
                notes: $row.find('.batch-note-input').val() || '',
                owner_corporation: $row.find('.batch-owner-corporation-select').val() || '',
                attacker_corporation: $row.find('.batch-attacker-corporation-select').val() || '',
                time_input: $row.find('input[name$="[time_input]"]').val() || '',
                role_id: $row.find('.batch-role-select').val() || '',
                tags: tags
            };
        }

        function focusBatchRow($row) {
            var $firstInput = $row.find('.batch-system-select').first();

            if ($firstInput.length) {
                $firstInput.select2('open');
            }

            $row[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function addBatchTimerRow(timerData, shouldFocus) {
            var rowKey = batchRowCounter++;
            var $row = $(buildBatchRow(rowKey, timerData));

            $('#batch-timer-rows').append($row);
            initializeBatchRow($row, timerData);
            refreshBatchRowTitles();
            activateBatchRow($row, true);

            if (shouldFocus) {
                focusBatchRow($row);
            }

            return $row;
        }

        function resetBatchRows(timerData) {
            $('#batch-timer-rows').empty();
            batchRowCounter = 0;

            var timers = timerData && timerData.length ? timerData : [{}];
            timers.forEach(function(timer) {
                addBatchTimerRow(timer, false);
            });

            var $lastRow = $('#batch-timer-rows .batch-timer-row').last();
            if ($lastRow.length) {
                activateBatchRow($lastRow, true);
            }
        }

        function resetEditForm() {
            $('#editTimerForm')[0].reset();
            $('#editTimerForm').attr('action', '');
            $('#edit_timer_id').val('');
            setSelectValue($('#edit_system'), '');
            $('#edit_structure_type').val(null).trigger('change');
            setSelectValue($('#edit_owner_corporation'), '');
            setSelectValue($('#edit_attacker_corporation'), '');
            $('#edit_notes').val('');
            $('#edit_role_id').val('').trigger('change');
            $('#editTimerForm .tag-checkbox').prop('checked', false).trigger('change');
            updateEditNoteButton();
        }

        initStructureTypeSelect($('#edit_structure_type'), $('#editTimerModal'));
        initRemoteSelect($('#edit_system'), $('#editTimerModal'), '{{ route("timerboard.search.systems") }}', 'Search for a system or celestial...', false);
        initRemoteSelect($('#edit_owner_corporation'), $('#editTimerModal'), '{{ route("timerboard.search.corporations") }}', 'Search for corporation or alliance...', false);
        initRemoteSelect($('#edit_attacker_corporation'), $('#editTimerModal'), '{{ route("timerboard.search.corporations") }}', 'Search for attacker (corp/alliance)...', true);

        @can('seat-timerboard.create')
            $('#create-timer-btn').click(function() {
                resetBatchRows([{}]);
                $('#batchTimerModal').modal('show');
            });

            $('#add-timer-row-btn, #add-timer-row-footer-btn').click(function() {
                addBatchTimerRow({}, true);
            });

            $('#duplicate-last-row-btn').click(function() {
                var $lastRow = $('#batch-timer-rows .batch-timer-row').last();

                if ($lastRow.length) {
                    addBatchTimerRow(collectBatchRowData($lastRow), true);
                } else {
                    addBatchTimerRow({}, true);
                }
            });

            $(document).on('click', '.duplicate-batch-row-btn', function() {
                var $row = $(this).closest('.batch-timer-row');
                addBatchTimerRow(collectBatchRowData($row), true);
            });

            $(document).on('click', '.remove-batch-row-btn', function() {
                var $row = $(this).closest('.batch-timer-row');
                var wasActive = $row.hasClass('is-active');

                if ($('#batch-timer-rows .batch-timer-row').length === 1) {
                    return;
                }

                var $nextRow = $row.next('.batch-timer-row');
                var $prevRow = $row.prev('.batch-timer-row');

                $row.remove();
                refreshBatchRowTitles();

                if (wasActive) {
                    activateBatchRow($nextRow.length ? $nextRow : $prevRow, true);
                }
            });
        @endcan

        $(document).on('click', '.batch-row-heading, .toggle-batch-row-btn', function(event) {
            event.preventDefault();

            var $row = $(this).closest('.batch-timer-row');
            activateBatchRow($row, false);
        });

        $(document).on('keydown', '.batch-row-heading', function(event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            activateBatchRow($(this).closest('.batch-timer-row'), false);
        });

        $(document).on('input change', '#batch-timer-rows input, #batch-timer-rows select', function() {
            var $row = $(this).closest('.batch-timer-row');
            updateBatchRowSummary($row);
        });

        $(document).on('focus', '#batch-timer-rows input, #batch-timer-rows select, #batch-timer-rows .select2-selection', function() {
            var $row = $(this).closest('.batch-timer-row');

            if ($row.length && !$row.hasClass('is-active')) {
                activateBatchRow($row, false);
            }
        });

        $(document).on('click', '.batch-note-btn', function() {
            var $row = $(this).closest('.batch-timer-row');

            openTimerNoteModal({
                mode: 'draft',
                editable: true,
                notes: $row.find('.batch-note-input').val() || '',
                title: buildNoteTitle($row.find('input[name$="[structure_name]"]').val(), $row.find('.batch-system-select').val()),
                subtitle: 'This note will be saved when you create the timer.',
                onApply: function(value) {
                    $row.find('.batch-note-input').val(value);
                    updateBatchRowNoteButton($row);
                }
            });
        });

        $(document).on('change', '.tag-checkbox', function() {
            var label = $('label[for="' + $(this).attr('id') + '"]');

            if ($(this).is(':checked')) {
                label.css('opacity', '1');
                label.css('box-shadow', '0 0 5px rgba(0,0,0,0.5)');
            } else {
                label.css('opacity', '0.5');
                label.css('box-shadow', 'none');
            }
        });

        $('.edit-timer-btn').click(function() {
            var timer = $(this).data('timer');
            var tags = $(this).data('tags') || [];
            var url = '{{ route("timerboard.update", ":id") }}'.replace(':id', timer.id);

            resetEditForm();
            $('#editTimerForm').attr('action', url);
            $('#edit_timer_id').val(timer.id);
            $('#edit_structure_name').val(timer.structure_name || '');
            $('#edit_notes').val(timer.notes || '');
            $('#edit_time_input').val(formatUtcTimestamp(timer.eve_time));
            setSelectValue($('#edit_system'), timer.system || '');
            $('#edit_structure_type').val(timer.structure_type || null).trigger('change');
            setSelectValue($('#edit_owner_corporation'), timer.owner_corporation || '');
            setSelectValue($('#edit_attacker_corporation'), timer.attacker_corporation || '');
            $('#edit_role_id').val(timer.role_id || '').trigger('change');

            $('#editTimerForm .tag-checkbox').prop('checked', false);
            tags.forEach(function(tagId) {
                $('#edit_tag_' + tagId).prop('checked', true);
            });
            $('#editTimerForm .tag-checkbox').trigger('change');
            updateEditNoteButton();

            $('#editTimerModal').modal('show');
        });

        $('#edit-note-btn').click(function() {
            openTimerNoteModal({
                mode: 'draft',
                editable: true,
                notes: $('#edit_notes').val() || '',
                title: buildNoteTitle($('#edit_structure_name').val(), $('#edit_system').val()),
                subtitle: 'This note will be saved when you update the timer.',
                onApply: function(value) {
                    $('#edit_notes').val(value);
                    updateEditNoteButton();
                }
            });
        });

        $('.timer-note-trigger').click(function() {
            var noteBody = $(this).siblings('.timer-note-content').val() || '';

            openTimerNoteModal({
                editable: false,
                notes: noteBody,
                title: $(this).attr('data-note-title') || 'Timer note',
                subtitle: $(this).attr('data-note-system') || 'Timerboard'
            });
        });

        $('#timer_notes').on('input', function() {
            updateTimerNoteCounter($(this).val());
        });

        $('#clearTimerNoteBtn').click(function() {
            $('#timer_notes').val('').trigger('input').focus();
        });

        $('#applyTimerNoteDraftBtn').click(function() {
            if (!noteModalApplyHandler) {
                return;
            }

            noteModalApplyHandler(normalizeNotes($('#timer_notes').val()));
            $('#timerNoteModal').modal('hide');
        });

        $('#timerNoteModal').on('hidden.bs.modal', function() {
            noteModalApplyHandler = null;
            $('#timer_notes').val('');
            $('#timerNoteReadonly').empty();
            updateTimerNoteCounter('');
        });

        $(document).on('select2:open', function(event) {
            activeSelect2Instance = $(event.target).data('select2') || null;

            requestAnimationFrame(function() {
                repositionSelect2Dropdown(activeSelect2Instance);
            });
        });

        $(document).on('select2:close', function() {
            activeSelect2Instance = null;
        });

        $('#batchTimerModal .modal-body, #editTimerModal .modal-body').on('scroll', function() {
            repositionSelect2Dropdown(activeSelect2Instance);
        });

        $(window).on('resize', function() {
            repositionSelect2Dropdown(activeSelect2Instance);
        });

        if (batchHadErrors) {
            resetBatchRows(batchOldTimers);
            $('#batchTimerModal').modal('show');
        }

        if (editHadErrors) {
            var timerId = '{{ old("timer_id") }}';
            var editUrl = '{{ route("timerboard.update", ":id") }}'.replace(':id', timerId);

            resetEditForm();
            $('#editTimerForm').attr('action', editUrl);
            $('#edit_timer_id').val(timerId);
            $('#edit_structure_name').val(oldEditValues.structure_name);
            $('#edit_notes').val(oldEditValues.notes);
            $('#edit_time_input').val(oldEditValues.time_input);
            setSelectValue($('#edit_system'), oldEditValues.system);
            $('#edit_structure_type').val(oldEditValues.structure_type).trigger('change');
            setSelectValue($('#edit_owner_corporation'), oldEditValues.owner_corporation);
            setSelectValue($('#edit_attacker_corporation'), oldEditValues.attacker_corporation);
            $('#edit_role_id').val(oldEditValues.role_id).trigger('change');

            $('#editTimerForm .tag-checkbox').prop('checked', false);
            if (oldEditValues.tags && oldEditValues.tags.length) {
                oldEditValues.tags.forEach(function(tagId) {
                    $('#edit_tag_' + tagId).prop('checked', true);
                });
            }
            $('#editTimerForm .tag-checkbox').trigger('change');
            updateEditNoteButton();

            $('#editTimerModal').modal('show');
        }

        $('.timers-table').DataTable({
            "order": [[ 5, "asc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [8, 10] }
            ],
            "stateSave": true,
            "paging": true,
            "pageLength": 25,
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
        });

        function updateTimers() {
            const now = new Date();
            const rows = document.querySelectorAll('.timer-row.active-timer');

            rows.forEach(row => {
                const timeStr = row.getAttribute('data-time');
                const eveTime = new Date(timeStr);
                const diff = eveTime - now;

                const localTimeCell = row.querySelector('.local-time');
                if (localTimeCell.textContent === 'Calculating...') {
                    localTimeCell.textContent = eveTime.toLocaleString();
                }

                const countdownCell = row.querySelector('.countdown');
                if (diff <= 0) {
                    countdownCell.textContent = 'ELAPSED';
                    countdownCell.classList.remove('text-warning');
                    countdownCell.classList.add('text-danger');
                    row.classList.remove('active-timer');
                    row.classList.add('static-timer');
                } else {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    let countdownStr = '';
                    if (days > 0) countdownStr += days + 'd ';
                    if (hours > 0) countdownStr += hours + 'h ';
                    countdownStr += minutes + 'm ' + seconds + 's';

                    countdownCell.textContent = countdownStr;

                    if (days == 0 && hours < 4) {
                        countdownCell.classList.add('text-warning');
                    }
                }
            });
        }

        function initStaticTimers() {
            const staticRows = document.querySelectorAll('.timer-row.static-timer');
            staticRows.forEach(row => {
                const timeStr = row.getAttribute('data-time');
                const eveTime = new Date(timeStr);
                const localTimeCell = row.querySelector('.local-time');
                if (localTimeCell && localTimeCell.textContent === 'Calculating...') {
                    localTimeCell.textContent = eveTime.toLocaleString();
                }
            });
        }

        initStaticTimers();
        setInterval(updateTimers, 1000);
        updateTimers();

        if (!batchHadErrors) {
            $('.tag-checkbox').trigger('change');
        }
    });
</script>
