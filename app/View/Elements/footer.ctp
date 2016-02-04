<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:07 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $this             View
 */
?>
<!-- START app/View/Elements/footer.ctp -->
<footer class="footer <?= $is_mb_app ? 'hide' : null ?>">
    <div class="footer-content">
        <div class="container">
            <div class="row">
                <div class="footer-col col-md-5 col-sm-7 col-sm-12 about">
                    <div class="footer-col-inner">
                        <h3 class="title">About Us</h3>
                        <p>東京・秋葉原にあるIT企業、株式会社ISAO。<br> ”世界のシゴトをたのしくするビジョナリーカンパニー”を中長期ビジョンに掲げています。 <br>2015年10月1日（木）より、日本初のバリフラットモデルを導入し、管理職０（ゼロ）、階層０（ナシ）、 チーム力∞（無限大）の組織運営をおこなっています。</p>
                        <p><a class="more" href="http://www.isao.co.jp/" target="_blank">HPを見る <i class="fa fa-long-arrow-right"></i></a></p>
                    </div><!--//footer-col-inner-->
                </div><!--//foooter-col-->
                <div class="footer-col col-md-3 col-sm-4 col-md-offset-1 links">
                    <div class="footer-col-inner">
                        <h3 class="title">Other Links</h3>
                        <ul class="list-unstyled">
                            <li><a href="terms.html"><i class="fa fa-caret-right"></i>利用規約</a></li>
                            <li><a href="privacy.html"><i class="fa fa-caret-right"></i>プライバシーポリシー</a></li>
                            <li><a href="law.html"><i class="fa fa-caret-right"></i>特定商取引法に基づく表記</a></li>
                        </ul>
                    </div><!--//footer-col-inner-->
                </div><!--//foooter-col-->
                <div class="footer-col col-md-3 col-sm-12 contact">
                    <div class="footer-col-inner">
                        <h3 class="title">Get in touch</h3>
                        <div class="row">
                            <p class="email col-md-12 col-sm-4"><i class="fa fa-envelope"></i><a href="/contact.html">お問い合わせ</a></p>
                            <p class="email col-md-12 col-sm-4"><i class="fa fa-microphone"></i><a href="/contact.html">プレス関連のお問い合わせ</a></p>
                            <p class="email col-md-12 col-sm-4"><i class="fa fa-heart"></i><a href="/contact.html">協業のお問い合わせ</a></p>
                        </div>
                    </div>
                </div>
            </div><!--//footer-col-inner-->
        </div><!--//foooter-col-->
    </div><!--//row-->
    <div class="bottom-bar">
        <div class="container">
            <div class="row">
                <small class="copyright col-md-6 col-sm-6 col-xs-12">© 2016 ISAO │　English (US)  　日本語</small>
                <ul class="social col-md-6 col-sm-6 col-xs-12 list-inline">
                    <li><a href="http://instagram.com/goalous" target=" _blank"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="https://twitter.com/goalous" target=" _blank"><i class="fa fa-twitter"></i></a></li>
                    <li class="last"><a href="https://www.youtube.com/user/Goalous" target=" _blank"><i class="fa fa-youtube"></i></a></li>
                    <li><a href="https://www.facebook.com/goalous/" target=" _blank"><i class="fa fa-facebook"></i></a></li>
                </ul><!--//social-->
            </div><!--//row-->
        </div><!--//container-->
    </div><!--//bottom-bar-->
</footer>
<div id="layer-black"></div>
<!-- END app/View/Elements/footer.ctp -->
