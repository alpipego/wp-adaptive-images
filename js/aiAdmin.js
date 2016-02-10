var AiAdmin = new function(target)
{
    this.pasteTarget = target;

    this.init = function()
    {
        this.newOrExisting(function(target) {
            AiAdmin.getImage(target, AiAdmin.initialVal(target));
        });
    }

    this.fillInputs = function(row, callback)
    {
        var inputs = {
            id: jQuery('[id$="'+ row +'-field_560c13a07b91a"]'),
            authorUrl: jQuery('[id$="'+ row +'-field_560bb295970d3"]'),
            licenseUrl: jQuery('[id$="'+ row +'-field_560bb2d0970d5"]'),
            authorName: jQuery('[id$="'+ row +'-field_560bb2b8970d4"]')
        }

        Object.defineProperty(AiAdmin, 'inputs', {
                value: inputs,
                writable: true,
                enumerable: true,
                configurable: true
            }
        );

        callback(inputs);
    }


    this.rows = function()
    {
        var fields = jQuery('[id$="field_560aabcf8dbef"').not('#acf-field_56a75b6e0f473-acfcloneindex-field_560aabcf8dbef');

        if (fields.length > 0) {
            return fields;
        }
    }

    this.newOrExisting = function(callback)
    {
        var fields = this.rows();

        if (typeof fields === 'undefined' || !fields) {
            return;
        }

        fields.each(function(index, val) {
            var fieldId = jQuery(val)[0].id;
            var repeater = fieldId.match('acf-field_(?:[a-zA-Z0-9]+)-([0-9]{1})-field_(?:[a-zA-Z0-9]+)');

            if (repeater) {
                AiAdmin.existingField(repeater[1]);
                callback(jQuery('#' + fieldId));
            } else {
                repeater = fieldId.match('acf-field_(?:[a-zA-Z0-9]+)-([a-zA-Z0-9]+)-field_(?:[a-zA-Z0-9]+)');
                AiAdmin.newField(repeater[1]);
            }
        });

    };

    this.existingField = function(row)
    {
        this.fillInputs(row, function(inputs) {
            jQuery(inputs.id).closest('.acf-field').hide();
        });
    }

    this.newField = function(row)
    {
        this.hideEmpty(row);
    }

    this.hideEmpty = function(row)
    {
        this.fillInputs(row, function(inputs) {
            jQuery.each(inputs, function() {
                jQuery(this).closest('.acf-field').hide();
            });
        });
    }

    this.handleInput = function(fieldData)
    {
        var target = jQuery(this.pasteTarget)[0].id;
        repeater = target.match('acf-field_(?:[a-zA-Z0-9]+)-([a-zA-Z0-9]+)-field_(?:[a-zA-Z0-9]+)');
        this.hideEmpty(repeater[1]);

        if (this.initialVal(this.pasteTarget) !== fieldData) {
            this.getImage(this.pasteTarget, fieldData);
        }
    }

    this.initialVal = function(target)
    {
        return jQuery(target).val();
    }

    this.getImage = function(target, url)
    {
        var ai = new adaptiveImages(target, this.inputs.id, this.inputs.authorUrl, this.inputs.licenseUrl, this.inputs.authorName);
        ai.checkUrlId(url);
    }
}

jQuery(document).ready(function($) {
    AiAdmin.init();

    $(document).on('click', '[data-event="add-row"], [data-event="remove-row"]', function() {
        AiAdmin.newOrExisting(function(){});
    });

    $(document)
        .on('paste', AiAdmin.rows(), function(e) {
            var pasteData = e.originalEvent.clipboardData.getData('text');
            AiAdmin.pasteTarget = $(e.target);
            AiAdmin.handleInput(pasteData);
        })
        .on('keypress', AiAdmin.rows(), function(e) {
            if (e.which === 13) {
                AiAdmin.pasteTarget = e.target;
                AiAdmin.handleInput($(e.target).val());
                return false;
            }
        });
});
