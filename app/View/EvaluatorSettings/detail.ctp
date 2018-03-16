<?= $this->App->viewStartComment()?>
<form method="post" action="" id="setEvaluators"><!-- TODO: -->
    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix eval-list">
        <div class="panel-heading">
            Set evaluators
        </div>
        <div class="panel-body eval-view-panel-body">
            <div class="form-group">
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                    <p class="font_bold"><?= __('Evaluatee') ?></p>
                </div>
                <div class="eval-list-item col-xxs-12">
                    <div class="eval-list-item-left">
                        <?=
                        $this->Upload->uploadImage($userEvaluatee, 'User.photo', ['style' => 'medium'],
                            ['width'  => '48px',
                             'height' => '48px',
                             'alt'    => 'icon',
                             'class'  => 'pull-left img-circle mtb_3px'
                            ]) ?>
                    </div>
                    <div class="eval-list-item-center">
                        <p class="font_bold"><?= h($userEvaluatee['User']['display_username']) ?></p>
                        <span class="font_bold">
                            <?php if (!is_null($userEvaluateeCoach)): ?>
                                Coach: <?=
                                $this->Upload->uploadImage($userEvaluateeCoach, 'User.photo', ['style' => 'medium'],
                                    ['width'  => '24px',
                                     'height' => '24px',
                                     'alt'    => 'icon',
                                     'class'  => 'img-circle mtb_3px'
                                    ]) ?> <?= h($userEvaluateeCoach['User']['display_username']) ?>
                            <?php else: ?>
                                Coach: <i class="fa fa-user" aria-hidden="true"></i> No Coach
                            <?php endif ?>
                        </span>
                    </div>
                </div>
                <hr class="col-xxs-12">
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                    <p class="font_bold"><?= __('Evaluators') ?></p>
                </div>
                <p>
                    TODO: Last update: *********
                </p>
                <div id="evaluators" class="list-group">
                    <?php foreach ($userEvaluators as $evaluatorKeyNumber => $userEvaluator): ?>
                    <div class="eval-list-item col-xxs-12 list-group-item">
                        <?php
                        $evaluatorNoDisplayed = ($evaluatorKeyNumber + 1);
                        $evaluatorsImageElementId = sprintf('evaluator_image_%d', $evaluatorKeyNumber);
                        $evaluatorsInputElementName = sprintf('evaluators[]');
                        ?>
                        <div class="evaluator_sort eval-list-item-left font_bold vertical-center horizontal-center">
                            <i class="fa fa-align-justify"></i>
                        </div>
                        <div class="evaluator_key_number eval-list-item-left font_bold vertical-center horizontal-center">
                        </div>
                        <div class="eval-list-item-left">
                            <?=
                            $this->Upload->uploadImage($userEvaluator, 'User.photo', ['style' => 'medium'],
                                ['width'  => '48px',
                                 'height' => '48px',
                                 'alt'    => 'icon',
                                 'class'  => 'pull-left img-circle mtb_3px',
                                 'id'     => $evaluatorsImageElementId,
                                ]) ?>
                        </div>
                        <div class="eval-list-item-center vertical-center">
                            <input type="hidden" class="form-input evaluator_select"
                                   name="<?= $evaluatorsInputElementName ?>"
                                   data-default-id="<?= $userEvaluator['User']['id'] ?>"
                                   data-default-text="<?= $userEvaluator['User']['display_username'] ?>"
                                   data-default-image="<?= $this->Upload->uploadUrl($userEvaluator, 'User.photo', ['style' => 'medium']) ?>"
                            />
                            &nbsp;
                            <button class="btn_remove btn btn-primary "><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
                <div class="pull-right">
                    <button id="button_add_evaluator" class="btn btn-primary"><i class="fa fa-plus fa-1x"></i> Add <span class="can_add_left"></span></button>
                </div>
            </div>
        </div>
        <div class="panel-footer addteam_pannel-footer">
            <div class="row">
                <div class="team-button pull-right">
                    <a class="btn btn-link design-cancel bd-radius_4px" data-dismiss="modal" href="<?= $this->Html->url(['controller'       => 'evaluator_settings', 'action'           => 'index',
                    ]) ?>">
                        <?= __('Cancel') ?>
                    </a>
                    <input class="btn btn-primary" type="submit" value="Save">
                </div>
            </div>
        </div>
    </div>
</form>
<div id="template_evaluator" class="hide">
    <div class="eval-list-item col-xxs-12 list-group-item">
        <div class="evaluator_sort eval-list-item-left font_bold vertical-center horizontal-center">
            <i class="fa fa-align-justify"></i>
        </div>
        <div class="evaluator_key_number eval-list-item-left font_bold vertical-center horizontal-center">
        </div>
        <div class="eval-list-item-left">
            <img src="/img/no-image-user.jpg" width="48px" height="48px" alt="icon" class="pull-left img-circle mtb_3px">
        </div>
        <div class="eval-list-item-center vertical-center">
            <input type="hidden" class="form-input evaluator_select" name="evaluators[]" />
            &nbsp;
            <button class="btn_remove btn btn-primary "><i class="fa fa-times"></i></button>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/goalous_evaluator_setting.min.js"></script>
<script>
    $(document).ready(function() {
        // setting sortable
        var el = document.getElementById('evaluators');
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
            var $evaluatorElement = $('#template_evaluator').children('div').first().clone();
            $evaluatorElement.removeClass('hide')

            $evaluatorsList.append($evaluatorElement)
            applySelect2ToElement($evaluatorElement.find('.evaluator_select').first())
            adjustFormsView()
            e.preventDefault()
            return true
        })
        $('#setEvaluators').on('submit', function (e) {
            // TODO: change to evaluator set api
            var form = $(this)
            $.post(
                '/v1/evaluator/set',
                form.serializeArray()
            ).done(function(data) {
                new Noty({
                    type: 'success',
                    text: '<h4>success</h4>TODO: save message',
                }).show();
            }).fail(function() {
                new Noty({
                    type: 'error',
                    text: '<h4>error</h4>TODO: fail message',
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
                var $evalListItem = $element.closest('.eval-list-item').first()
                $element.on("change", function(e) {
                    $evalListItem.find("img").attr('src', e.added['image']);
                    // this is getting the hidden form to submit value
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
            var left = MAX_EVALUATORS - evaluatorsCount;
            if (left > 0) {
                $('#button_add_evaluator').find(".can_add_left").text("(You can add " + left + " more)")
            }
            if (left === 0 || hasEmptyEvaluator) {
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
</script>
<?= $this->App->viewEndComment()?>
