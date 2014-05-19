<?
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 4/30/14
 * Time: 5:52 PM
 * @var $this View
 **/
?>
<? if (GOOGLE_ANALYTICS_ID): ?>
    <!-- start Google Analytics -->
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', '<?= GOOGLE_ANALYTICS_ID?>', 'goalous.com');
    ga('send', 'pageview');

</script>
<!-- end Google Analytics -->
<? endif; ?>
<!-- start VWO and Mixpanel Integration Code-->
<script type="text/javascript">
    var _vis_opt_queue = window._vis_opt_queue || [], _vis_counter = 0, mixpanel = window.mixpanel || [];
    _vis_opt_queue.push(function() {
        try {
            if(!_vis_counter) {
                var _vis_data = {},_vis_combination,_vis_id,_vis_l=0;
                for(;_vis_l<_vwo_exp_ids.length;_vis_l++) {
                    _vis_id = _vwo_exp_ids[_vis_l];
                    if(_vwo_exp[_vis_id].ready) {
                        _vis_combination = _vis_opt_readCookie('_vis_opt_exp_'+_vis_id+'_combi');
                        if(typeof(_vwo_exp[_vis_id].combination_chosen) != "undefined")
                            _vis_combination = _vwo_exp[_vis_id].combination_chosen;
                        if(typeof(_vwo_exp[_vis_id].comb_n[_vis_combination]) != "undefined") {
                            _vis_data['VWO-Test-ID-'+_vis_id] = _vwo_exp[_vis_id].comb_n[_vis_combination];
                            _vis_counter++;
                        }
                    }
                }
                // Use the _vis_data object created above to fetch the data,
                // key of the object is the Test ID and the value is Variation Name
                if(_vis_counter) mixpanel.push(['register_once', _vis_data]);
            }
        }
        catch(err) {};
    });
</script>
<!-- end VWO and Mixpanel Integration Code-->
<? if (VWO_ID): ?>
    <!-- Start Visual Website Optimizer Asynchronous Code -->
    <script type='text/javascript'>
    var _vwo_code=(function(){
        var account_id =<?= VWO_ID?>,
            settings_tolerance=2000,
            library_tolerance=2500,
            use_existing_jquery=false,
// DO NOT EDIT BELOW THIS LINE
            f=false,d=document;return{use_existing_jquery:function(){return use_existing_jquery;},library_tolerance:function(){return library_tolerance;},finish:function(){if(!f){f=true;var a=d.getElementById('_vis_opt_path_hides');if(a)a.parentNode.removeChild(a);}},finished:function(){return f;},load:function(a){var b=d.createElement('script');b.src=a;b.type='text/javascript';b.innerText;b.onerror=function(){_vwo_code.finish();};d.getElementsByTagName('head')[0].appendChild(b);},init:function(){settings_timer=setTimeout('_vwo_code.finish()',settings_tolerance);this.load('//dev.visualwebsiteoptimizer.com/j.php?a='+account_id+'&u='+encodeURIComponent(d.URL)+'&r='+Math.random());var a=d.createElement('style'),b='body{opacity:0 !important;filter:alpha(opacity=0) !important;background:none !important;}',h=d.getElementsByTagName('head')[0];a.setAttribute('id','_vis_opt_path_hides');a.setAttribute('type','text/css');if(a.styleSheet)a.styleSheet.cssText=b;else a.appendChild(d.createTextNode(b));h.appendChild(a);return settings_timer;}};}());_vwo_settings_timer=_vwo_code.init();
</script>
<!-- End Visual Website Optimizer Asynchronous Code -->
<? endif; ?>
<? if (MIXPANEL_TOKEN): ?>
    <!-- start Mixpanel -->
    <script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
        for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"===e.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f)}})(document,window.mixpanel||[]);
        mixpanel.init("<?= MIXPANEL_TOKEN?>");</script>
    <!-- end Mixpanel -->
    <!-- start Uservoice -->
<script type="text/javascript">

    UserVoice=window.UserVoice||[];(function(){
        var uv=document.createElement('script');uv.type='text/javascript';
        uv.async = true;
        uv.src = '//widget.uservoice.com/<?= USERVOICE_API_KEY?>.js';
        var s=document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(uv,s)})();
</script>
<!-- end Uservoice -->
<? endif; ?>
