define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
], function ($, modal, $t) {
    'use strict';

    return {
        config: {},
        mappings: [],

        init: function (config) {
            this.config = config;
            this.themeOptions = config.themeOptions || {};
            this.handleOptions = config.handleOptions || {};
            this.loadExistingData();
            this.bindEvents();
        },

        loadExistingData: function () {
            var self = this;
            var existingData = this.config.existingData || [];

            if (typeof existingData === 'string') {
                try {
                    existingData = JSON.parse(existingData);
                } catch (e) {
                    existingData = [];
                }
            }

            this.mappings = existingData;
            if (this.mappings.length === 0) {
                this.addMapping();
            } else {
                this.renderMappings();
            }
        },

        bindEvents: function () {
            var self = this;

            $('#' + this.config.addButtonId).on('click', function () {
                self.addMapping();
            });

            $(document).on('click', '.pointeger-themeswitcher-remove', function () {
                var index = $(this).data('index');
                self.removeMapping(index);
            });
        },

        addMapping: function () {
            this.mappings.push({
                theme: '',
                handles: []
            });
            this.renderMappings();
            this.updateHiddenInput();
        },

        removeMapping: function (index) {
            this.mappings.splice(index, 1);
            this.renderMappings();
            this.updateHiddenInput();
        },

        renderMappings: function () {
            var self = this;
            var html = '';

            $.each(this.mappings, function (index, mapping) {
                html += '<div class="pointeger-themeswitcher-mapping-row" data-index="' + index + '">';
                html += '<h3 style="margin-top: 0;">' + $t('Mapping') + ' #' + (index + 1) + '</h3>';

                html += '<div class="admin__field">';
                html += '<label class="admin__field-label"><span>' + $t('Theme') + '</span></label>';
                html += '<div class="admin__field-control">';
                html += '<select class="admin__control-select pointeger-theme-select" data-index="' + index + '" style="width: 100%;">';
                html += '<option value="">' + $t('-- Please Select --') + '</option>';

                $.each(self.themeOptions, function (value, label) {
                    var selected = mapping.theme === value ? 'selected' : '';
                    html += '<option value="' + self.escapeHtml(value) + '" ' + selected + '>' + self.escapeHtml(label) + '</option>';
                });

                html += '</select>';
                html += '</div>';
                html += '</div>';

                html += '<div class="admin__field">';
                html += '<label class="admin__field-label"><span>' + $t('XML Handles') + '</span></label>';
                html += '<div class="admin__field-control">';
                html += '<select class="admin__control-select pointeger-handles-select" multiple="multiple" data-index="' + index + '" size="10" style="width: 100%;">';

                $.each(self.handleOptions, function (value, label) {
                    var selected = $.inArray(value, mapping.handles || []) !== -1 ? 'selected' : '';
                    html += '<option value="' + self.escapeHtml(value) + '" ' + selected + '>' + self.escapeHtml(label) + '</option>';
                });

                html += '</select>';
                html += '<small style="display: block; margin-top: 5px;">' + $t('Hold Ctrl/Cmd to select multiple handles') + '</small>';
                html += '</div>';
                html += '</div>';

                html += '<div class="admin__field">';
                html += '<button type="button" class="action-secondary pointeger-themeswitcher-remove" data-index="' + index + '">' + $t('Remove This Mapping') + '</button>';
                html += '</div>';
                html += '</div>';
            });

            $('#' + this.config.listId).html(html);

            // Bind change events
            $('.pointeger-theme-select, .pointeger-handles-select').on('change', function () {
                self.updateMappingFromUI();
            });
        },

        updateMappingFromUI: function () {
            var self = this;

            $.each(this.mappings, function (index, mapping) {
                var themeSelect = $('.pointeger-theme-select[data-index="' + index + '"]');
                var handlesSelect = $('.pointeger-handles-select[data-index="' + index + '"]');

                mapping.theme = themeSelect.val() || '';
                mapping.handles = handlesSelect.val() || [];
            });

            this.updateHiddenInput();
        },

        updateHiddenInput: function () {
            var value = JSON.stringify(this.mappings);
            $('#' + this.config.inputId).val(value);
        },

        escapeHtml: function (text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };
});
