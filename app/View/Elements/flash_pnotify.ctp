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
<script type="text/javascript">
    new PNotify({
        title: '<?=$title?>',
        text: '<?=$message?>',
        icon: '<?=$icon?>',
        type: '<?=$type?>'
    });
</script>
<i class="fa-info-circle"