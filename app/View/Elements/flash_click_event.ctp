<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/27
 * Time: 1:23
 *
 * @var $id
 */
?>
<!-- START app/View/Elements/flash_click_event.ctp -->
<script type="text/javascript">
    $(document).ready(function () {
        $("#<?=$id?>").trigger('click');
    });
</script>
<!-- END app/View/Elements/flash_click_event.ctp -->
