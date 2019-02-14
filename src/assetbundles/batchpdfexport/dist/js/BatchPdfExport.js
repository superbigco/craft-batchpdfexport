/**
 * Batch PDF Export plugin for Craft CMS
 *
 * Batch PDF Export JS
 *
 * @author    Superbig
 * @copyright Copyright (c) 2018 Superbig
 * @link      https://superbig.co
 * @package   BatchPdfExport
 * @since     1.0.0
 */
BatchPDFActionTrigger = Craft.ElementActionTrigger.extend({
    init: function(settings) {
        this.setSettings(settings, Craft.ElementActionTrigger.defaults);
        var $triggers = $('[id^=' + settings.type.replace(/[\[\]\\]+/g, '-') + '-actiontrigger]');
        var $trigger = $triggers.first();

        if ($triggers.length > 1) {
            $triggers.each(function(index, element) {
                var $currentTrigger = $(element);
                if (settings.label === $(element).text()) {
                    $trigger = $currentTrigger
                }
            });
        }

        this.$trigger = $trigger;

        // Do we have a custom handler?
        if (this.settings.activate) {
            // Prevent the element index's click handler
            this.$trigger.data('custom-handler', true);

            // Is this a custom trigger?
            if (this.$trigger.prop('nodeName') === 'FORM') {
                this.addListener(this.$trigger, 'submit', 'handleTriggerActivation');
            }
            else {
                this.addListener(this.$trigger, 'click', 'handleTriggerActivation');
            }
        }

        this.updateTrigger();
        Craft.elementIndex.on('selectionChange', $.proxy(this, 'updateTrigger'));
    },
    defaults: {
        type: null,
        batch: true,
        validateSelection: null,
        activate: null,
        label: null,
    }
});