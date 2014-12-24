<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/27
 * Time: 1:23
 *
 * @var $message
 * @var $title
 * @var $icon
 * @var $type
 */
?>
<!-- START app/View/Elements/flash_pnotify.ctp -->
<script type="text/javascript">
    new PNotify({
        title: "<?=$title?>",
        text: "<?=$message?>",
        icon: "<?=$icon?>",
        type: "<?=$type?>",
        delay: 4000,
        mouse_reset: false
    });
</script>
<!-- END app/View/Elements/flash_pnotify.ctp -->
