<?
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 4:55 PM
 *
 * @var $title_for_layout string
 * @var $this             View
 */
?>
<!-- START app/View/Elements/head.ctp -->
<head>
    <?= $this->Html->charset(); ?>
    <title origin-title="<?= $title_for_layout; ?>">
        <?= $title_for_layout; ?>
    </title>
    <?
    echo $this->Html->meta('icon');
    echo $this->Html->meta(
        ['name'    => 'viewport',
         'content' => "width=device-width, initial-scale=1, maximum-scale=1"
        ]);
    echo $this->Html->meta(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);
    //TODO botの拒否。一般公開前に必ず外す。
    echo $this->Html->meta(['name' => 'ROBOTS', 'content' => 'NOINDEX,NOFOLLOW']);

    //    echo $this->Html->css('bw-simplex.min', array('media' => 'screen'));
    //    echo $this->Html->css('bw-simplex', array('media' => 'screen'));
    echo $this->Html->css('goalstrap', array('media' => 'screen'));
    echo $this->Html->css('jasny-bootstrap.min');
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('jquery.nailthumb.1.1');
    echo $this->Html->css('bootstrapValidator.min');
    echo $this->Html->css('bootstrap-switch.min');
    echo $this->Html->css('pnotify.custom.min');
    echo $this->Html->css('lightbox');
    echo $this->Html->css('showmore');
    echo $this->Html->css('bootstrap-ext-col');
    echo $this->Html->css('customRadioCheck.min');
    echo $this->Html->css('select2');
    echo $this->Html->css('select2-bootstrap');
    echo $this->Html->css('bootstrap-ext-col');
    echo $this->Html->css('datepicker3');
    echo $this->Html->css('style', array('media' => 'screen'));
    echo $this->fetch('css');
    echo $this->fetch('meta');

    ?>
    <!--suppress HtmlUnknownTarget -->
    <link href="/img/apple-touch-icon.png" rel="apple-touch-icon-precomposed">
    <!--[if lt IE 9]>
    <?= $this->Html->script('vendor/html5shiv')?>
    <?= $this->Html->script('vendor/respond.min')?>
    <![endif]-->
    <?
    //公開環境のみタグを有効化
    if (PUBLIC_ENV) {
        /** @noinspection PhpDeprecationInspection */
        echo $this->element('external_service_tags');
    }
    ?>
</head>
<!-- END app/View/Elements/head.ctp -->
