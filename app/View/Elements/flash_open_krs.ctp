<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/27
 * Time: 1:23
 *
 * @var $goal_id
 */
?>
<!-- START app/View/Elements/flash_open_krs.ctp -->
<script type="text/javascript">
    $(document).ready(function () {
        $("#" + "KRsOpen_<?=$goal_id?>").trigger('click');
    });
</script>
<!-- END app/View/Elements/flash_open_krs.ctp -->
