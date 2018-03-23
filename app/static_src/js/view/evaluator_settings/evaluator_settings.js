$(document).ready(function() {
    // setting sortable
    var el = document.getElementById('evaluators');
    if (el === null) {
        return;
    }
    var sortable = Sortable.create(el, {
        handle: '.evaluator_sort',
        chosenClass: 'list-group-item-success',
        animation: 150,
        onEnd: function (evt) {
            adjustFormsView()
        },
    });

    var MAX_EVALUATORS = 7
    var $evaluatorsList = $('#evaluators')
    $(document).on('click', '.btn_remove', function(e) {
        $(this).closest('.eval-list-item').remove()
        adjustFormsView()
        e.preventDefault()
        return true
    })
    $('#button_add_evaluator').on('click', function(e) {
        var $evaluatorElement = $('#template_evaluator').children('li').first().clone();
        $evaluatorElement.removeClass('hide')

        $evaluatorsList.append($evaluatorElement)
        applySelect2ToElement($evaluatorElement.find('.evaluator_select').first())
        adjustFormsView()
        e.preventDefault()
        return true
    })
    $('#setEvaluators').on('submit', function (e) {
        var form = $(this)
        $.post(
            '/api/v1/evaluators',
            form.serializeArray()
        ).done(function(data) {
            new Noty({
                type: 'success',
                text: '<h4>success</h4>TODO: save message',
            }).show();
        }).fail(function(xhr) {
            var data = $.parseJSON(xhr.responseText);
            new Noty({
                type: 'error',
                text: '<h4>error</h4>' + data.message,
            }).show();
        }).always(function() {
            form.find('input[type=submit]').prop("disabled", false);
        })
        e.preventDefault()
    })
    // see select2 document: https://select2.github.io/select2/ (3.5.3)
    function applySelect2ToElement(element) {
        element.each(function(i, e) {
            var $element = $(e)
            $element.on("change", function(e) {
                // Setting the value to submit on hidden form
                var userId = e.added["id"].replace("user_", "")
                $(this).closest(".eval-list-item").find("input[type=hidden]").val(userId)
                adjustFormsView()
            }).select2({
                formatResult: select2Format,
                formatSelection: select2Format,
                minimumInputLength: 1,
                query: function (query) {
                    if (query.term === undefined) {
                        return;
                    }
                    $.get('/users/ajax_select2_get_users', {
                        term: query.term,
                        page_limit: 10,
                        _: (new Date()).getTime()
                    }).done(function (data) {
                        query.callback(data);
                    })
                },
                initSelection: function (element, callback) {
                    var $this = $(element);
                    callback({
                        id: $this.data("default-id"),
                        text: $this.data("default-text"),
                        image: $this.data("default-image")
                    })
                }
            })
            if (undefined != $element.data('default-id')) {
                $element.select2('val', $element.data('default-id'))
            }
        })
    }
    function adjustFormsView() {
        // set the key number of evaluators
        var numbering = 1
        $evaluatorsList.find('.evaluator_key_number').each(function(i, e) {
            $(e).text(numbering++)
        })
        // show/hide evaluator add button
        var hasEmptyEvaluator = false
        $evaluatorsList.find('.evaluator_select').each(function(i, e) {
            var val = $(e).select2("val")
            if ("" === val) {
                hasEmptyEvaluator = true
            }
        })
        var evaluatorsCount = $evaluatorsList.find('.eval-list-item').length
        var remaining = MAX_EVALUATORS - evaluatorsCount;
        if (remaining > 0) {
            $('#remaining_to_add').text(remaining + " remaining ")
        }
        if (remaining === 0 || hasEmptyEvaluator) {
            $('#button_add_evaluator').addClass('hide')
        } else {
            $('#button_add_evaluator').removeClass('hide')
        }
    }
    function select2Format(state) {
        if (!state.id) return state.text; // optgroup
        return "<img src='" + state.image + "' width='16' height='16' />" + state.text;
    }
    (function () {
        var elements = $('.evaluator_select')
        applySelect2ToElement(elements)
        adjustFormsView()
    })();
});