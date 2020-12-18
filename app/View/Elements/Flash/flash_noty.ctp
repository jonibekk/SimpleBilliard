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
    var link_url = "<?= $link_url ?>"
    var link_text = "<?= $link_text ?>"
    var link = ""
    if (link_url !== "") {
        link = "<br /><br /><a href='" + link_url + "'>" + link_text + "</a>"
    }
    console.log(link)
    
    new Noty({
        type: "<?=$type?>",
        text: '<h4>'+'<?=$title?>'+'</h4>'+'<?=$message?>'+link,
        closeWith: ['click'],
        callbacks: {
            onClick: function() {
                if (link_url !== "") {
                    window.location.href = link_url;
                }
            }
        }
    }).show();
</script>
<?= $this->App->viewEndComment()?>
