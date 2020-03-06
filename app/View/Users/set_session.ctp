<?php
/**
 * @var CodeCompletionView $this
 * @var CodeCompletionView $jwt_token
 * @var CodeCompletionView $redirect_url
 */
?>
<?= $this->App->viewStartComment()?>
<script>
window.onload = function() {
    localStorage.setItem('token', '<?= $jwt_token ?>');
    window.location.href = '<?= $redirect_url ?>';
}
</script>
<?= $this->App->viewEndComment()?>
