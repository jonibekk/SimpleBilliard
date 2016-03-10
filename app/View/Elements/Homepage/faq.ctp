<?php /**
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var CodeCompletionView $this
 * @var                    $user_count
 * @var                    $top_lang
 */
?>
<!-- START app/View/Elements/Homepage/faq.ctp -->

<?php $faq = [
        [
            'question' => __('他の社内向けSNSと何が違いますか？'),
            'answer' => __('「ゴールでつながる社内SNS」であることが決定的な違いです。ゴール（目標）がメンバーによって作成され、そのゴールをフォローやコラボすることでゴールと繋がることができます。ゴールに対して、だれがいつなにをしたかが「フォトアクション」（画像つきの記事）としてホームフィードに掲載され、みんながたのしくチェックできます。
また、他の社内SNSにあるような、特定のメンバーをグルーピングして閉じた空間で情報交換することももちろん可能です。')
        ],
        [
            'question' => __('個人の目標評価はどのようにおこなうのですか？'),
            'answer' => __('個人の目標による評価は、期末もしくは来期中に実施することができます。コーチによって認定（評価対象として承認）されたゴールに対してのゴール評価と、そのメンバー個人のトータル評価の2分類があり、あらかじめチーム（組織）で定めたスコアとコメントを評価者が入力します。コーチは1人のみ、評価者は最大7人まで設定でき（兼務可能）、評価者は定められた順番で評価を実施できます。さらに設定によって、「自己評価」と「最終者評価（CSVファイルでの一括評価）」を評価者として追加できます。')
        ],
        [
            'question' => __('社員が期中で退職したらどうなりますか？'),
            'answer' => __('チーム管理者（Goalous上での最高権限をもつチームメンバー）が、退職した社員をチームから「非アクティブ（無効）化」処理をしてください。非アクティブ化された社員は、そのチームへのアクセス権を失います。よって、一切の情報を参照することはできません。また、一度非アクティブ化されたメンバーを復活することもできます。なお、非アクティブ化されたメンバーの情報は、チーム内に残りますので、データが削除されるわけではございません。')
        ],
        [
            'question' => __('セキュリティやバックアップはどうなっていますか？'),
            'answer' =>__('データの分散保管をしていますので、仮に特定のデーターセンターが災害に遭っても別のデーターセンターで稼働します。また、すべてのデータは、常時バックアップをしていますので、1台のサーバーに障害が発生してもデータを復旧できます。不正アクセスへのセキュリティとしては、SSLによる暗号化通信・2段階認証・ログインロックに加え、定期的に第三者機関による脆弱性診断を実施しております。
なお、弊社はプライバシーマークの取得企業ですのでご安心ください。')
        ],
        [
            'question' => __('スマートフォン・タブレットのアプリはありますか？'),
            'answer' => __('Android, iPhoneの専用アプリケーションをご用意しております。それぞれのストアからダウンロードしてご利用ください。スマートフォンやタブレットからブラウザでアクセスいただいても、最適化されたビューでご利用できます。')
        ],
        [
            'question' => __('企業担当者向けに詳しい説明をしてもらえますか？'),
            'answer' => __('ぜひぜひ！お問い合わせフォームからご連絡くださいませ。お待ちしております。')
        ],
        [
            'question' => __('無償期間（2016年8月31日まで）の後はどうなりますか？'),
            'answer' => __('機能制限（メンバーの追加不可能など）を予定しておりますが、データの削除などせず引き続きご利用いただけます。')
        ],
        [
            'question' => __('機能のカスタマイズはできますか？') ,
            'answer' => __('カスタマイズにはご対応いたしません。ユーザー様からのご要望はいつでも歓迎いたします。ご要望がサービスの改善に役立つと判断した場合は、実装を計画いたしますのでよろしくお願いします。')
        ],
        [
            'question' => __('使い方がわからない場合、サポートはありますか？'),
            'answer' => __('2016年8月31日までの無料キャンペーン中にご登録いただいたお客様は、ログイン後に利用できるサポートツールでお問い合わせください。できる限り早い対応をいたします。')
        ],
        [
            'question' => __('Goalousの運営会社はどこですか？'),
            'answer' => __('"たのしい！"をうみだしとどける企業、ISAO（いさお）が運営しております。豊田通商株式会社100％出資の子会社です。1999年に創業を開始しており、オープン・チャレンジ・キズナをスピリッツとした集団です。')
        ],
    ]
?>
<!-- ******FAQ****** -->
<section id="faq" class="faq section has-bg-color">
    <div class="container">
        <h2 class="title text-center"><?= __('Frequent questions') ?></h2>
        <div class="row faq-lists">
            <?php foreach ($faq as $key => $value): ?>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-parent="#accordion" data-toggle="collapse" class="panel-toggle" href="#faq<?= $key ?>">
                                    <i class="fa fa-plus-square"></i>
                                    <!-- 質問文 -->
                                    <?= $value['question'] ?>
                                </a>
                            </h4>
                        </div>

                        <div class="panel-collapse collapse" id="faq<?= $key ?>">
                            <div class="panel-body">
                                <!-- 回答 -->
                                <?= $value['answer'] ?>
                            </div>
                        </div>
                    </div><!--//panel-->
                </div>
                <!-- 左右を2コ1にするために右側のfaqのあとにパーティションを挿入 -->
                <?php if( $key%2 === 1 ){ echo '<hr class="faq-partition col-xs-12">'; } ?>
            <?php endforeach; ?>

        </div><!--//row-->
        <div class="more text-center col-md-6 col-md-offset-3">
            <h4 class="title"><?= __('Any other question?') ?></h4>
            <?= $this->Html->link(__('Contact us'), array('controller' => 'contact'),
                                  array('class' => 'btn btn-cta btn-cta-secondary btn-lg btn-block')); ?>
        </div>
    </div><!--//container-->
</section><!--//faq-->
<!-- END app/View/Elements/Homepage/faq.ctp -->
