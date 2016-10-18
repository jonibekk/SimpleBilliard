<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/27
 * Time: 1:23
 *
 * @var CodeCompletionView $this
 * @var                    $id
 */
?>
<?= $this->App->viewStartComment()?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#<?=$id?>").trigger('click');
    });
</script>
<?= $this->App->viewEndComment()?>
