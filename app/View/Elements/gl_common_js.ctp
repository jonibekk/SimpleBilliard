<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/22/14
 * Time: 6:47 PM
 */
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