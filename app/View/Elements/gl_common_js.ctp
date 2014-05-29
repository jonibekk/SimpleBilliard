<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/22/14
 * Time: 6:47 PM
 */
?>
<?
echo $this->Html->script('jquery-2.1.0.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('bootstrapValidator.min');
echo $this->Html->script('bvAddition');
echo $this->Html->script('pnotify.custom.min');
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
    (function () {
    }());
</script>
<?
echo $this->fetch('script');
echo $this->Session->flash('pnotify');
//環境を識別できるようにリボンを表示
switch (ENV_NAME) {
    case 'stg':
        echo $this->Html->script('http://quickribbon.com/ribbon/2014/04/c966588e9495aa7b205aeaaf849d674f.js');
        break;
    case 'local':
        echo $this->Html->script('http://quickribbon.com/ribbon/2014/04/b13dfc8e5d887b8725f256c31cc1dff4.js');
        break;
    default:
        break;
}
?>
