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
<!-- START app/View/Elements/Homepage/signup.ctp -->
<!-- ******SIGNUP****** -->
<section id="signup" class="signup">
    <div class="container text-center">
        <h2 class="title"><?= __d('lp', 'さぁ、Goalous Teamへ！') ?></h2>
        <p class="summary"><?= __d('lp', '2016年8月31日まで完全無料！今すぐお試しください。') ?></p>
        <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'register']) ?>">
            <button type="submit" class="btn btn-cta btn-cta-primary"><?= __d('lp', '新規登録') ?></button>
        </a>
    </div>
</section><!--//signup-->
<!-- END app/View/Elements/Homepage/signup.ctp -->
