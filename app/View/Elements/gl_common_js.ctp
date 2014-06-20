<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/22/14
 * Time: 6:47 PM
 */
?>
<?
//echo $this->Html->script('jquery-2.1.0.min');
echo $this->Html->script('jquery-1.11.1.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('jasny-bootstrap.min');
echo $this->Html->script('bootstrapValidator.min');
echo $this->Html->script('bvAddition');
echo $this->Html->script('pnotify.custom.min');
echo $this->Html->script('jquery.nailthumb.1.1.min');
echo $this->Html->script('placeholders.min');
echo $this->Html->script('gl_basic');
?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.validate').bootstrapValidator({
                live: 'enabled',
                feedbackIcons: {
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                },
                fields: {
                    "data[User][password]": {
                        validators: {
                            stringLength: {
                                min: 8,
                                message: '<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>'
                            }
                        }
                    },
                    "data[User][password_confirm]": {
                        validators: {
                            identical: {
                                field: "data[User][password]",
                                message: '<?=__d('validate', "パスワードが一致しません。")?>'
                            }
                        }
                    }
                }
            });
        });
    </script>
<?
echo $this->Session->flash('pnotify');
//環境を識別できるようにリボンを表示
?>
<? if (ENV_NAME == "stg"): ?>
    <div class="ribbon">
        <span style='margin-left:50px;'>Staging</span>
    </div>
<? elseif (ENV_NAME == "local"): ?>
    <div class="ribbon">
        <span style='margin-left:50px;'>Local</span>
    </div>
<?endif; ?>