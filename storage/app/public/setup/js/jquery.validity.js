/**
 * validity - jQuery validation plugin (https://github.com/gustavoconci/validity.js)
 * Copyright (c) 2017-2018, Gustavo Henrique Conci. (MIT Licensed)
 */

(function($) {
    $.fn.validity = function(settings) {
        var defaultSettings = {
                ignore: ':hidden'
            },
            settings = Object.assign({}, defaultSettings, settings),
            selector = ':input:not(' + settings.ignore + ')';

        var $forms = $(this),
            inputValidity = function($form, $inputs) {
                return function(e) {
                    var $this = $(this);
                    if (e.type === 'keyup' && (
                        !$this.val() ||
                        ($this.is(':radio') || $this.is(':checkbox') && !$this.is(':checked'))
                    )) return;
                    if (!$this.attr('name')) return;
                    if ($this.is(':radio')) {
                        $this = $inputs.filter('[name="' + $this.attr('name') + '"]');
                        if (!$this.prop('required')) {
                            return;
                        }
                    }
                    if (!$this.attr('required')) return;

                    var validity = $this[0].validity;
                    if (validity.valid && !validity.typeMismatch && !validity.patternMismatch) {
                        if ($this.is(':file, :radio, :checkbox')) {
                            $this.parent().addClass('valid').removeClass('error mismatch');
                        } else {
                            $this.addClass('valid').removeClass('error mismatch');
                        }
                        $this.parent('div').next('label.error').remove();
                    } else {
                        if ($this.is(':file, :radio, :checkbox')) {
                            $this.parent().addClass('error').removeClass('valid');
                        } else {
                            $this.addClass('error').removeClass('valid');
                        }
                        $form.data('valid', false);
                        if (validity.valueMissing && $this.attr('data-missing')) {
                            $this.parent('div').next('label.error').remove();
                            $this.parent('div').after('<label for="' + $this.attr('id') + '" class="error">' + $this.attr('data-missing') + '</label>');
                        }
                        if (e.type == 'focusout') {
                            if (validity.patternMismatch || validity.typeMismatch) {
                                $this.addClass('mismatch');
                                if ($this.attr('data-mismatch')) {
                                    $this.parent('div').next('label.error').remove();
                                    $this.parent('div').after('<label for="' + $this.attr('id') + '" class="error">' + $this.attr('data-mismatch') + '</label>');
                                }
                            }
                        }
                    }
                };
            };

        $forms.each(function() {
            var $this = $(this),
                $inputs = $this.find(selector);
            $this.data('valid', true);

            $this
                .attr('novalidate', true)
                .off('keyup.validity change.validity focusout.validity')
                .on('keyup.validity change.validity focusout.validity', selector, inputValidity($this, $inputs))
                .off('valid.validity')
                .on('valid.validity', function(e, $inputs) {
                    $this.data('valid', true);
                    $inputs.each(inputValidity($this, $inputs));
                });
        });

        $.fn.valid = function() {
            var $this = $(this);
            $this.trigger('valid', [$this.find(selector)]);
            return $this.data('valid');
        };

        $.fn.reset = function() {
            var $this = $(this);
            $this.find(':input').removeClass('valid error mismatch')
                .filter(':file').parent().removeClass('valid error mismatch');
            $this[0].reset();
            return $this;
        };

        return $forms;
    };
})(jQuery);