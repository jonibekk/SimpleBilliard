<?php
/**
 * @var array  $translationTeamDefaultLanguage
 * @var int    $translationTeamTotalUsage
 * @var int    $translationTeamTotalLimit
 * @var string $translationTeamResetText
 * @var array  $translationTeamLanguageCandidates
 */
?>
<?= $this->App->viewStartComment() ?>
    <section class="panel panel-default">
        <header>
            <h2><?= __("Translation Settings") ?></h2>
        </header>
        <div class="panel-body">
            <?php if (empty($translationTeamLanguageCandidates)) : ?>
                <?= __('Translation feature is disabled now. If you would like to use it,') ?>
                <a href="mailto:<?= INTERCOM_APP_ID ?>@incoming.intercom.io"
                   class="intercom-launcher "><?= __('please contact us.') ?></a>
            <?php else : ?>
                <div class="form-group">
                    <label class="col col-sm-3 control-label form-label sm-text-right">
                        <p><?= __('Selected Translation Language') ?></p>
                    </label>
                    <div><?= implode(", ", $translationTeamLanguageCandidates) ?></div>
                </div>
                <hr>
                <div>
                    <label class="col col-sm-3 control-label form-label sm-text-right">
                        <p><?= __('Default Translation Language') ?></p>
                    </label>
                    <div><?= array_values($translationTeamDefaultLanguage)[0] ?></div>
                </div>
                <hr>
                <div>
                    <label class="col col-sm-3 control-label form-label sm-text-right">
                        <p><?= __('Translation Usage') ?></p>
                    </label>
                    <div class="col col-sm-9">
                        <p>
                            <?php if ($translationTeamTotalUsage > $translationTeamTotalLimit) : ?>
                                <span style="color:#d9230f"><b><?= number_format($translationTeamTotalUsage) ?></b></span>
                            <?php elseif ($translationTeamTotalUsage > 0.9 * $translationTeamTotalLimit) : ?>
                                <span><b><?= number_format($translationTeamTotalUsage) ?></b></span>
                            <?php else: ?>
                                <span><?= number_format($translationTeamTotalUsage) ?></span>
                            <?php endif ?>
                            <span><?= " / " . number_format($translationTeamTotalLimit) . " " . __("characters") ?></span>
                        </p>
                        <p class="form-control-static">
                            <?php if ($translationTeamTotalUsage > $translationTeamTotalLimit) : ?>
                                <?= __("You have reached your translation limit.") ?> <br>
                                <?= __("Please wait until reset.") ?> <br>
                                <?= __('If you would like to change your limit,') ?>
                                <a href="mailto:<?= INTERCOM_APP_ID ?>@incoming.intercom.io"
                                   class="intercom-launcher "><?= __('please contact us.') ?></a>
                            <?php elseif ($translationTeamTotalUsage > 0.9 * $translationTeamTotalLimit) : ?>
                                <?= __("You will soon reach your translation limit.") ?> <br>
                                <?= __("If the limit is exceeded, translation feature will be unavailable until reset.") ?>
                                <br>
                                <?= __('If you would like to change your limit,') ?>
                                <a href="mailto:<?= INTERCOM_APP_ID ?>@incoming.intercom.io"
                                   class="intercom-launcher "><?= __('please contact us.') ?></a>
                            <?php endif ?>
                        </p>
                    </div>
                </div>
                <hr style="clear:both;">
                <div>
                    <label class="col col-sm-3 control-label form-label sm-text-right">
                        <p><?= __("Reset Date") ?></p></label>
                    <div><?= $translationTeamResetText ?></div>
                </div>
                <hr>
                <div>
                    <?= __('If you would like to change the settings,') ?>
                    <a href="mailto:<?= INTERCOM_APP_ID ?>@incoming.intercom.io"
                       class="intercom-launcher "><?= __('please contact us.') ?></a>
                </div>
            <?php endif ?>
        </div>
    </section>
<?= $this->App->viewEndComment() ?>