<?
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 4/24/15
 * Time: 00:43
 */
?>
<!-- START app/View/Elements/google_tag_manager.ctp -->
<?if(GOOGLE_TAG_MANAGER_ID):?>
<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?=GOOGLE_TAG_MANAGER_ID?>>"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?=GOOGLE_TAG_MANAGER_ID?>');</script>
<!-- End Google Tag Manager -->
<?endif;?>
<!-- END app/View/Elements/google_tag_manager.ctp -->