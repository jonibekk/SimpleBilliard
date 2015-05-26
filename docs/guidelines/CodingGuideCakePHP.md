# CakePHPのリファレンス
- [Lib](http://book.cakephp.org/2.0/ja/core-libraries.html)
 - [イベントシステム](http://book.cakephp.org/2.0/ja/core-libraries/events.html#id1)をできれば複雑ないくつかの処理に利用したい。
- [Model](http://book.cakephp.org/2.0/ja/models.html)
 - [JOIN句](http://book.cakephp.org/2.0/ja/models/associations-linking-models-together.html#id6)複雑なjoinが発生する場合に使う。しかし、基本的には、[Containable](http://book.cakephp.org/2.0/en/core-libraries/behaviors/containable.html)を使います。
 - 共通の検索条件がある場合は[カスタムfind](http://book.cakephp.org/2.0/ja/models/retrieving-your-data.html#model-custom-find)をうまく使う
 - [Model::read()](http://book.cakephp.org/2.0/ja/models/retrieving-your-data.html#model-read)は使わない
 - [サブクエリ](http://book.cakephp.org/2.0/ja/models/retrieving-your-data.html#id6)の使い方
 - [`Model::create`を使う場合の注意点](http://book.cakephp.org/2.0/ja/models/saving-your-data.html#model-create-array-data-array)
 - [HABTMはhasMayとbelongsToに分解する](http://book.cakephp.org/2.0/ja/models/saving-your-data.html#id2)
 - [`delete`](http://book.cakephp.org/2.0/ja/models/deleting-data.html#delete)は、必要に応じて`cascade`を有効にして、関連するデータも削除する
 - [`recursive`は-1に設定する](http://book.cakephp.org/2.0/ja/models/model-attributes.html#recursive)
 - 複数の更新クエリがある場合は[トランザクション](http://book.cakephp.org/2.0/ja/models/transactions.html#id1)を使う
 - [Containableの代替え](https://gist.github.com/gothedistance/5931772)
- [View](http://book.cakephp.org/2.0/ja/views.html)
- [Controller](http://book.cakephp.org/2.0/ja/controllers.html)
 - Postのリクエストがあった場合に、Postされたデータを検索条件にしてfindする場合は、[`Controller::postConditions`](http://book.cakephp.org/2.0/ja/controllers.html#Controller::postConditions)を使う。
 - [Etagヘッダ](http://book.cakephp.org/2.0/ja/controllers/request-response.html#etag)でキャッシュをコントロールする？
- [Component](http://book.cakephp.org/2.0/ja/controllers/components.html)
- [Behavior](http://book.cakephp.org/2.0/ja/models/behaviors.html)
- [Helper](http://book.cakephp.org/2.0/ja/views/helpers.html)
- [Plugin](http://book.cakephp.org/2.0/ja/plugins.html)
- [Test](http://book.cakephp.org/2.0/ja/development/testing.html)
- [Console](http://book.cakephp.org/2.0/ja/console-and-shells.html)
- [REST](http://book.cakephp.org/2.0/ja/development/rest.html)
- [例外について](http://book.cakephp.org/2.0/ja/development/exceptions.html)

# Goalous内のルール
## 基本
- コミット前はリフォーマットする。
- Modelになるべく処理を書き、Controller側はスリムにする。
- Modelの共通処理は`AppModel`に書く。`callback`を用いる場合は`Behavior`に書く。
- Controllerの共通処理は`AppController`に書く。メソッドが多くなる場合は`Component`に切り出す。
- Controllerで利用可能なComponent、Modelでアソシエーションを張っているModelは、PHPDocに必ず書いておく。
- View内で用いるphpは可読性向上の為に`if`と`foreach`のみになるべく限定する。
- View内で`if`,`foreach`のネストが頻発する場合は、Viewに渡す変数を分ける。
- Viewの共通処理は`Helper`に書く。

## Model
#### リカーシブは -1が基本
以下はAppModelの設定。
```php
    //全てのモデルでデフォルトで再起的にjoinするのをやめる。個別に指定する。
    public $recursive = -1;
```

belongsToもしくはhasManyのテーブルを一緒に取ってきたい場合は原則`contain`キーで指定する。  
[Containableビヘイビアのリファレンス](http://book.cakephp.org/2.0/en/core-libraries/behaviors/containable.html)

`$rescursive`を変更する場合は`find`後に必ず元の設定に戻す。

#### ログインユーザIDと現在のチームID、自分のユーザ情報は全Modelの`__construct`でメンバー変数に格納している(将来的にはやめる)
書きかけです。


#### トランザクション処理は`AppModel`に書いてある
書きかけです。


#### タイプやステータスはメンバ定数を作成し、`__construct`で名前を設定する
理由としては、

- メンバ定数 ... コード補完が効くのでController、Viewから参照しやすい。
- 名前を`__construct`で指定する ... gettext形式(`__d('gl',"hoge")`)が変数定義時に利用できない為。

#### 複数のModelを参照するfindを書く場合は、元になるModelにメソッドを書く。
書きかけです。


#### 権限チェックなどは例外を投げる。
書きかけです。


## Controller
#### レスポンスを返す処理はアクションメソッド内で`return`する。
理由:  
- レスポンスを返す処理がアクションメソッドにあると処理が終わる箇所が把握しやすい。
- `return`しないと、テストでそのあとの処理が実行されてしまう。

以下はリファラにリダイレクト。
```php
return $this->redirect($this->referer());
```
以下はその時点でレスポンスを返す。
```php
return $this->render();
```
あるページにリダイレクトさせたい場合は、決まってこのルールに従います。
例(実在しないパラメータです)：
```php
return $this->redirect(['controller'=>'goals','action'=>'add',111,'type'=>'abc']);
```
この場合は`GoalsController`の`add`アクションメソッドが呼ばれ、そのメソッドの第一引数に`111`が渡り、  
名前付きパラメータの`type`が`$this->request->params['named']['type']`に渡ります。  
`/goals/add/111/type:abc`ではなく、なぜこのような書き方にするかというと、以下のファイルのルーティングルールに準拠する為です。  
https://github.com/IsaoCorp/goalous2/blob/develop/app/Config/routes.php  
ここで指定すれば、長いURLを短くカスタマイズしたり、色々できます。逆ルーティングも含めて。  
ソースコードに手を加えずに、サービス全体に反映させられます。  


#### 権限チェックなどは、`try-catch`で処理する。
書きかけです。


#### Ajaxの場合は必ず共通の処理前と処理後のメソッドを呼ぶ。
書きかけです。


#### deleteメソッドはPOST以外を受け付けない。
書きかけです。



## View
書きかけです。  
現在、[BoostCake](http://slywalker.github.io/cakephp-plugin-boost_cake/)というプラグインを使ってFormその他の出力を行っているが、近い将来これをやめ、Cake標準の[FormHelper](http://book.cakephp.org/2.0/ja/core-libraries/helpers/form.html)に戻す。  
理由としては、当初Bootstrapを採用した事で共通のelementの出力をHelperがまかなってくれることでViewの行数が減って良かったが、多様なComponentに対応できず、かつ極端に可読性が落ちている為。

## Test
### Controller
#### Mockを必ず使う。
書きかけです。

#### assertionチェックは不要。カバレッジを上げる事を目的とする。
書きかけです。


### Model
#### assertionチェックを行う。
書きかけです。


### View
テスト不要。
