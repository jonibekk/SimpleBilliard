<?php /**
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var CodeCompletionView $this
 * @var                    $user_count
 * @var                    $top_lang
 */
?>
<?= $this->App->viewStartComment()?>
<!-- ******SIGNUP****** -->
<section id="signup" class="signup">
    <div class="container text-center">
        <h2 class="title"><?= __('Let\'s go to Goalous!') ?></h2>
        <?php if($isLoggedIn): ?>
        <p class="row">
            <a href="<?= $this->Html->url(['controller' => 'pages', 'action' => 'home']) ?>"
               class="col-md-6 col-md-offset-3">
                <button type="submit" class="btn btn-cta btn-cta-primary btn-block btn-lg"><?= __('Go Your Team') ?></button>
            </a>
        </p>
        <?php else: ?>      
        <p class="summary"><?= __("Easy set-up ･ Free 15 day Trial") ?></p>
        <p class="row">
            <a href="<?= $this->Html->url(['controller' => 'signup', 'action' => 'email', '?' => ['type' => 'bottom']]) ?>"
               class="col-md-6 col-md-offset-3" id="RegisterLinkBottom">
                <button type="submit" class="btn btn-cta btn-cta-primary btn-block btn-lg"><?= __('Create New Team') ?></button>
            </a>
        <?php endif;?>
        </p>
        <?php if(!$isLoggedIn): ?>
        <p><?= __('Are you on Goalous? %s. Any questions ? %s.', '<a href="/users/login">' . __('Login') . '</a>', '<a href="/contact">' . __('Contact us') . '</a>') ?></p>
        <?php endif; ?>
    </div>
</section><!--//signup-->
<?= $this->App->viewEndComment()?>
