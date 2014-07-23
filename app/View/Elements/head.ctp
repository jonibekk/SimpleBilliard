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
<head>
    <?= $this->Html->charset(); ?>
    <title>
        <?= $title_for_layout; ?>
    </title>
    <?
    echo $this->Html->meta('icon');
    echo $this->Html->meta(
                    ['name'    => 'viewport',
                     'content' => "width=device-width, initial-scale=1, maximum-scale=1"
                    ]);
    echo $this->Html->meta(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);
    //echo $this->Html->css('bootstrap.min.css', array('media' => 'screen'));
    echo $this->Html->css('bw-simplex.min', array('media' => 'screen'));
    //    echo $this->Html->css('bw-simplex', array('media' => 'screen'));
    echo $this->Html->css('jasny-bootstrap.min');
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('jquery.nailthumb.1.1');
    echo $this->Html->css('bootstrapValidator.min');
    echo $this->Html->css('pnotify.custom.min');
    echo $this->Html->css('lightbox');
    echo $this->Html->css('showmore');
    echo $this->Html->css('bootstrap-ext-col');
    echo $this->Html->css('style', array('media' => 'screen'));
    echo $this->fetch('css');
    echo $this->fetch('meta');
    ?>
    <!--[if lt IE 9]>
    <?= $this->Html->script('html5shiv')?>
    <?= $this->Html->script('respond.min')?>
    <![endif]-->
    <?
    //公開環境のみタグを有効化
    if (PUBLIC_ENV) {
        /** @noinspection PhpDeprecationInspection */
        echo $this->element('external_service_tags');
    }
    ?>
</head>