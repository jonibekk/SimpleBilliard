<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/27
 * Time: 1:23
 *
 * @var CodeCompletionView $this
 * @var                    $message
 * @var                    $title
 * @var                    $icon
 * @var                    $type
 */
?>
<?= $this->App->viewStartComment()?>
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
<?= $this->App->viewEndComment()?>
