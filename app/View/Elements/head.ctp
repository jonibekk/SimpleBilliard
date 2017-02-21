<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 4:55 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $this             View
 * @var                    $meta_description
 */
?>
<?= $this->App->viewStartComment()?>
<head>
    <?= $this->Html->charset(); ?>
    <title origin-title="<?= $title_for_layout; ?>">
        <?= $title_for_layout; ?>
    </title>
    <meta name='description' content='<?= $meta_description ?>'/>
    <?php echo $this->Html->meta('icon');
    echo $this->Html->meta(
        [
            'name'    => 'viewport',
            'content' => "width=device-width, height=device-height, initial-scale=1, maximum-scale=1, user-scalable=no"
        ]);
    echo $this->Html->meta(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

    // クリックジャッキング対策
    echo $this->Html->meta(['name' => 'X-FRA#2960ME-OPTIONS', 'content' => 'SAMEORIGIN']);

    echo $this->fetch('meta');
    echo $this->fetch('css');

    echo $this->Html->css('goalous.min', array('media' => 'screen'));
    echo $this->Html->script('/js/goalous.prerender.min');

    ?>
    <?php if ($this->request->params['action'] === 'display'): //上部のタブメニューの表示切替えの為?>
        <style>
            @media screen and (max-width: 991px) {
                #jsLeftSideContainer {
                    top: 100px;
                }
            }
        </style>
        <script type="text/javascript">
            if (window.matchMedia('screen and (max-width:991px)').matches) {
                $(function () {
                    $(window).scroll(function () {
                        if ($(this).scrollTop() > 1) {
                            $("#jsLeftSideContainer").stop().animate({"top": "60px"}, 200)
                        } else {
                            $("#jsLeftSideContainer").stop().animate({"top": "100px"}, 100);
                        }
                    });
                });
            } else {
                // Nothing
            }
        </script>
    <?php endif; ?>

    <!--suppress HtmlUnknownTarget -->
    <link href="/img/apple-touch-icon.png" rel="apple-touch-icon-precomposed">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Oswald:400,300|Pathway+Gothic+One">
    <!--[if lt IE 9]>
    <?= $this->Html->script('vendor/html5shiv')?>
    <?= $this->Html->script('vendor/respond.min')?>
    <![endif]-->
    <?php //公開環境のみタグを有効化
    if (PUBLIC_ENV) {
        /** @noinspection PhpDeprecationInspection */
        echo $this->element('external_service_tags');
    }
    ?>
</head>
<?= $this->App->viewEndComment()?>
