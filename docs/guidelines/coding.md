<hr id="css">
# CSSガイド

このドキュメントでは、CSSにおけるプロパティの挙動について一切の説明をしておりません。  
セレクタの優先順位、`position`や`float`、`overflow`、`z-index`といったものについての使用方法などは知っているという前提です。  
その上で、実用に耐えうるものにするためのガイドラインです。  

1. [Tools - ツール](#1-tools)
2. [Components - コンポーネント](#2-components)
    - [Modifiers - モディファイア](#2-1-modifiers)
    - [State - ステート](#2-2-state)
    - [Media Queries - メディアクエリ](#2-3-media-queries)
    - [Keeping It Encapsulated - カプセル化の維持](#2-4-keeping-it-encapsulated)
3. [JavaScript](#3-javascript)
4. [Mixins - ミックスイン](#4-mixins)
5. [Utilities - ユーティリティ](#5-utilities)
6. [File Structure - ファイル構造](#6-file-structure)
7. [Style - スタイル](#7-style)
8. [Miscellany - その他](#8-miscellany)
    - [Performance - パフォーマンス](#performance)

<hr id="1-tools">
## 1. Tools - ツール

> CSSプリプロセッサで使うのはimportsと変数、ミックスイン(ベンダープリフィックスのために)だけにする。

CSSをリーダブルな状態にしておくために、CSSには余計なものを書かないようにしましょう。私たちはLESSを使っていますが、`imports`、`data-uri`、`変数`、`ミックスイン`という限られたものしか使いません。  
~~しかも、mixinはベンダープレフィックスのためにしか使いません。~~  
`imports`を使って`変数`と`mixins`がどこでも使えるようにして、ひとつのファイルにまとめましょう。ネストを使うこともありますが、階層が浅くなるように使いましょう。`&:hover`などがその例です。`guards`や`loops`といったLESSの複雑な機能は使わないようにしましょう。

これから書くルールにしたがえば、君はプリプロセッサの複雑な機能を使わない方がよいです。

<hr id="2-components">
## 2. Components - コンポーネント

> `.component-descendant-descendant` というパターンを使ってコンポーネント化するべきです。

コンポーネントという考え方を適用することで、CSSをカプセル化できます。また、CSSがわかりにくくなるのを避けて、リーダブルでメンテナブルにたもつことができます。CSSのコンポーネント化の核は名前空間<sup>[用語]()</sup>です。  
`.header img{ ... }`といった具合の入れ子のセレクタを使うのではなく、あたらしくハイフン区切りでクラスを子要素にあてましょう。`.header-image {...}`といった具合になります。

入れ子のセレクタを使ったサンプルコードをまずお見せします。

``` LESS
.global-header {
  background: hsl(202, 70%, 90%);
  color: hsl(202, 0%, 100%);
  height: 40px;
  padding: 10px;
}

.global-header .logo {
  float: left;
}

.global-header .logo img {
  height: 40px;
  width: 200px;
}

.global-header .nav {
  float: right;
}

.global-header .nav .item {
  background: hsl(0, 0%, 90%);
  border-radius: 3px;
  display: block;
  float: left;
  -webkit-transition: background 100ms;
  transition: background 100ms;
}

.global-header .nav .item:hover {
  background: hsl(0, 0%, 80%);
}
```

そして次が名前空間を採用した例です。内容はまったくいっしょです。

``` LESS
.global-header {
  background: hsl(202, 70%, 90%);
  color: hsl(202, 0%, 100%);
  height: 40px;
  padding: 10px;
}

  .global-header-logo {
    float: left;
  }

    .global-header-logo-image {
      background: url("logo.png");
      height: 40px;
      width: 200px;
    }

  .global-header-nav {
    float: right;
  }

    .global-header-nav-item {
      background: hsl(0, 0%, 90%);
      border-radius: 3px;
      display: block;
      float: left;
      -webkit-transition: background 100ms;
      transition: background 100ms;
    }

    .global-header-nav-item:hover {
      background: hsl(0, 0%, 80%);
    }
```

名前空間のおかげで、CSSのポイントの低さを維持できます。なので、インラインや`!important`で上書きする場面も少なくできて、長期にわたってメンテナブルにできます。

**すべてのセレクタをクラスにする** ということを守りましょう。idを使うこともエレメントセレクタで指定することも避けましょう。アンダースコア`under_score`区切りも、キャメルケース`camelCase`もやめましょう。そして文字はすべて小文字を使いましょう。  

コンポーネントのおかげで、クラス間の関係がわかりやすくできます。名前を見ればわかります。**子要素のクラスにインデントをつける** ようにしましょう。こうすることで関係をはっきりさせ、ファイル全体の見通しがよくなります。ただし`:hover`などといった状態を表すものは同じインデントにしておきましょう。

<hr id="2-1-modifiers">
### Modifiers - モディファイア 部分的な調整

> モディファイアのクラスには、`.component-descendant.mod-modifier`というパターンを使いましょう。

コンポーネント化しましょう、ですがスタイルは特別な方法です。
クラス同士は親子関係ではなく兄弟関係のようなため、名前空間に問題が発生することがあります。`.component-descendant-modifier`という名前を付けることは、`-modifier`の部分が子要素と勘違いしやすいです。モディファイアだと示すために、`.mod-modifier`クラスを使おう。

たとえば、ヘッダーボタンの中にサインアップボタンだけ特別にしたい場合を考えよう。その場合は`.global-header-nav-item.mod-sign-up`としてスタイルをあてましょう。
その例は、次の通りです。

``` HTML
<!-- HTML -->

<a class="global-header-nav-item mod-sign-up">
  Sign Up
</a>
```

``` LESS
// global-header.less

.global-header-nav-item {
  background: hsl(0, 0%, 90%);
  border-radius: 3px;
  display: block;
  float: left;
  -webkit-transition: background 100ms;
  transition: background 100ms;
}

.global-header-nav-item.mod-sign-up {
  background: hsl(120, 70%, 40%);
  color: #fff;
}
```

`global-header-nav-item`のスタイルを継承して、`.mod-sign-up`を付随的に書けます。これは名前空間の規則を破っています。しかし、これこそが私たちの求めるものです。つまり、私たちはファイルの読み込み順に悩まなくてよくなります。透明性のために、コンポーネントのパートの後にモディファイアを書きましょう。
部分的な調整剤なので、モディファイアはコンポーネントと同じインデントレベルに置きます。

**決して`.mod-`だけのクラスにスタイルをあててはいけません。**
つまり、`.header-button.mod-sign-up { background: green; }` はよい例ですが、 `.mod-sign-up { background: green; }` はわるい例です。 `.mod-sign-up { background: green; }` は他の場所でも使いうるモディファイアであって、そこに上書きされるような影響を残さないようにしたいです。

あなたは、モディファイアのついている要素の子要素にCSSを書きたい状況があると思います。
次のような感じです。

``` LESS
.global-header-nav-item.mod-sign-up {
  background: hsl(120, 70%, 40%);
  color: #fff;

  .global-header-nav-item-text {
    font-weight: bold;
  }

}
```

基本的に、ネストは避けたいものです。なぜなら、結果としてルールをなし崩して、読みにくいものにするからです。ただし、この場合は例外です。

モディファイアはコンポーネントファイルの一番下に書きましょう。オリジナルのコンポーネントより後ろに書くということです。

<hr id="2-2-state">
### State - ステート

> ステートのクラスには、`.component-descendant.is-state`というパターンを使いましょう。 `.is-`というクラスをJavaScriptで操作しましょう。（ただし、プレゼンテーションのクラスは除きます）

ステート(状態)のclassは次のようなことを表現します。
- 有効(enabled)
- 拡張(expanded)
- 非表示(hidden)
- または何を持っているのか。
これらのclassには、`.component-descendant.is-state`といったあたらしいパターンを使います。

例 - ヘッダーのロゴをクリックしたら、ホームに戻るようになっている。シングルページアプリケーションとして描画する部分だけ読み込みます。すると、描画部分でないロゴにはローディングのアニメーションを付けたくなりますよね。  
以下の画像のような具合です。

![DuRSLIjddi.gif](https://qiita-image-store.s3.amazonaws.com/0/58800/06358a51-8eb1-3b26-e0fd-2c4e932d023a.gif "DuRSLIjddi.gif")

その際に、使うclassは`.global-header-logo-image.is-loading`という具合になります。

``` LESS
.global-header-logo-image {
  background: url("logo.png");
  height: 40px;
  width: 200px;
}

.global-header-logo-image.is-loading {
  background: url("logo-loading.gif");
}
```

JavaScriptはアプリケーションの状態を定義します。そのため、ステートのクラスを入れ替えたりするためにJavaScriptを使います。`.component.is-state`のパターンは、ステートとプレゼンテーションの関心を非干渉化します。そのため、プレゼンテーションのクラスについて気にすることなく、ステートのクラスを付け加えられます。開発者はデザイナーに、「このエレメントは`.is-loading`のクラスを持っているので、スタイル加えたければそちらにどうぞ。」と言えばよいだけです。  
もし、ステート(状態)を表すクラスが`global-header-logo-image--is-loading`のようなものだった場合、開発者はpresentation層についてたくさん知らなければならず、将来的な変更が大変なことになります。  

モディファイア同様に、ステートのクラスも違ったコンポーネント上で使うことはできます。あなたがスタイルの上書きあるいは継承を望まないならば、**すべてのコンポーネントはステート毎に独自のスタイルを定義すること** が重要となります。つまり、`.global-header.is-hidden { display: none; }`という例はよいですが、`.is-hidden { display: none; }`とは、書かないことです(こう書きたくなりますが……)。`.is-hidden`は違うコンポーネント上では、違うものを意味するかもしれないからです。

また、ステートのクラスはインデントしません。繰り返しですが、インデントは子要素だけです。ステートのクラスは、コンポーネントやモディファイアといったものの後に、つまりファイルの下の方に書きましょう。

<hr id="2-3-media-queries">
### Media Queries - メディアクエリ

> メディアクエリはコンポーネントファイルに書きましょう。

`mobile.less`といった具合に、モバイル専用の特別なルールをまとめたファイルをつくるのは誰でもやりたくなることだと思います。ダメです。  
また、グローバルなメディアクエリは避けましょう。メディアクエリは、グローバルにではなくコンポーネントの中に書くべきと考えます。
この方法でなら、コンポーネントをアップデートしたり削除したりするときに、メディア毎のルールを忘れずに済むようになります。

毎回メディアクエリを書くのではなく、media-queries.lessファイルにメディアクエリのための変数を用意しています。
中身は次のようになります。

``` LESS
@highdensity:  ~"only screen and (-webkit-min-device-pixel-ratio: 1.5)",
               ~"only screen and (min--moz-device-pixel-ratio: 1.5)",
               ~"only screen and (-o-min-device-pixel-ratio: 3/2)",
               ~"only screen and (min-device-pixel-ratio: 1.5)";

@small:        ~"only screen and (max-width: 750px)";
@medium:       ~"only screen and (min-width: 751px) and (max-width: 900px)";
@large:        ~"only screen and (min-width: 901px) and (max-width: 1280px)";
@extra-large:  ~"only screen and (min-width: 1281px)";

@print:        ~"print";
```

そして実際にメディアクエリを使う際には次のように書きます。

``` LESS
// Input - 入力
@media @large {
  .component-nav { … }
}

/* Output - 出力 */
@media only screen and (min-width: 901px) and (max-width: 1280px) {
  .component-nav { … }
}
```

カンマ区切りで複数の変数をつなぐこともできます。

つまり、これは同じブレークポイントを使っていて、何度もメディアクエリを書かなくて済むようになります。メディアクエリのように、繰り返して書かれるものは、かんたんにまとめられるので圧縮効果がよいです。つまり、あなたはCSSサイズが大きくなりすぎることを懸念する必要はありません。この手法は、[the CodePen from Eric Rasch](http://codepen.io/ericrasch/pen/HzoEx)によるものです。

printはメディアの属性のひとつに過ぎないことを書いておきます。printルールはコンポーネントの中に書きましょう。

mediaのルールは、コンポーネントファイルの下の方に書いておきましょう。

<hr id="2-4-keeping-it-encapsulated">
## Keeping It Encapsulated - カプセル化の維持

コンポーネントは大きなレイアウトや単なるボタンまでコントロールします。テンプレートには、ひとつのコンポーネントが他のコンポーネントの中にあるということはたくさんあるでしょう。たとえば、`.member-list`の中での`.button`です。私たちは、リストに最適化するようにボタンのサイズとポジションを変更する必要があります。

コンポーネントは、お互いに一切の干渉がないようにするべきです。もし、よりちいさいボタンを複数の場所で再利用するなら、モディファイアをボタンのコンポーネントに添えて(たとえば`.button.mod-small`のように)、`.member-list`の中で使いましょう。メンバーリストが指定されるべきで、ボタンが指定されるべきでないので、メンバーリストのコンポーネントと子要素を使います。

例を挙げましょう:

``` HTML
<!-- HTML -->

<div class="member-list">
  <div class="member-list-item">
    <p class="member-list-item-name">Gumby</p>
    <div class="member-list-item-action">
      <a href="#" class="button mod-small">Add</a>
    </div>
  </div>
</div>
```

``` LESS
// button.less

.button {
  background: #fff;
  border: 1ps solid #999;
  padding: 8px 12px;
}

.button.mod-small {
  padding: 6px 10px;
}


// member-list.less

.member-list {
  padding: 20px;
}

  .member-list-item {
    margin: 10px 0;
  }

    .member-list-item-name {
      font-weight: bold;
      margin: 0;
    }

    .member-list-item-action {
      float: right;
    }
```

*わるい* 書き方はこうです:

``` HTML
<!-- HTML -->

<div class="member-list">
  <div class="member-list-item">
    <p class="member-list-item-name">Pat</p>
    <a href="#" class="member-list-item-button button">Add</a>
  </div>
</div>
```

``` LESS
// member-list.less

.member-list-item-button {
  float: right;
  padding: 6px 10px;
}
```

わるい例の中では、`.member-list-item-button`がボタンコンポーネントのスタイルを上書きします。ボタンについて書かれたものは、ボタンについて一切関与していないように見えてします。また、small buttonのスタイルを再利用しにくくしていて、透明性のあるコードを維持することを難しくしています。そのせいで、後々の変化に対応しにくくなります。

たくさんのコンポーネントをまとめましょう。常に、以下のことを問いつづけましょう。  
コンポーネントは常に関連付いているか？コンポーネントを分割できないか？もし、たくさんのモディファイアと子要素があったら、分割するときかもしれません。

<hr id="3-javascript">
## 3. JavaScript

> 見栄えと振る舞いに関連するクラスを分けましょう。`.js-`というプリフィックスをJSのためのクラスに付けましょう。

たとえば、

``` HTML
<!-- HTML -->

<div class="content-nav">
  <a href="#" class="content-nav-button js-open-content-menu">
    Menu
  </a>
</div>
```

``` JavaScript
// JavaScript (with jQuery)

$(".js-open-content-menu").on("click", function(e){
  openMenu();
});
```

なぜ、このようなことをするのでしょうか？`.js-`というクラスをつけることで、次にテンプレートに変更を加える人に、「JavaScriptのイベントで使われていますよ」という注意喚起をうながすためです。  

しっかりと**説明的なクラス名を使う**ようにしましょう。`.js-open-content-menu`が伝える情報は、`.js-open`が伝えるものよりもより明確です。また、より説明的なクラス名であれば、他のクラス名と衝突する可能性が減少しますし、検索も非常に簡単になります。JSのためのクラスには、ほとんど常に動詞を含ませるべきです。なぜなら、動きに結びついているからです。

**`.js-`といったクラスは、決してスタイルシート上に現れてはいけません。** なぜなら、JavaScriptのためのクラスだからです。逆も同様に、`.header-nav-button`のような見栄えを表すクラスは決してJavaScriptの中で現れるべきではありません。
ステートのクラス`.is-state`はJavaScriptに書かれて、スタイルシートでは`.component.is-state`といった具合で書かれます。

<hr id="4-mixins">
## 4. Mixins - ミックスイン

> ミックスインには`.m-`というプリフィックスを付けましょう。ミックスインで共通のスタイルをつくるのは控えめにしましょう。

ミックスインは複数のコンポーネントで使用される共通のスタイルです。ミックスインはスタンドアローンなクラスでもなく、実際にHTML上にマークアップされるクラスでもありません。ミックスインは、単一レベルであるべきで、ネストされないようにするべきです。  
ミックスインは、またたく間にコードを複雑にします。そのため**控えめに使用する**べきです。

かつて、私たちはミックスインをベンダープレフィックスのために使用していましたが、今では[autoprefixer](https://www.npmjs.com/package/autoprefixer)によりベンダープレフィックスの付与をしています。

ミックスインを使用するとき、それがミックスインのために使用されているということを明示するために、丸括弧をきちんと付けるべきです。
例:

``` LESS
// mixins.less
.m-list-divider () {
  border-bottom: 1px solid @light-gray-300;
}

// component.less
.component-descendent {
  .m-list-divider();
}
```

※ 訳注 - CSSプリプロセッサの機能なので詳細には書かないが、丸括弧が与える効果をかんたんに補足しておきます。

mixinは引数を取ることができます。ただ、引数を取らずに取る場合は、丸括弧ありの書き方と丸括弧なしの書き方ができます。
その差は、丸括弧つきか否かで、コンパイル後のファイルに残るか残らないかです。
下の例をご参照ください。

```LESS
.mixin-test () {
  color: #000;
}

.mixin-test {
  color: #fff;
}
```

コンパイルすると以下のようになります。

```CSS
.mixin-test {
  color: #fff;
}
```

丸括弧を付けることにより、それはmixinだと明示的に表して余計な記述が残らないようにするべきです。

<hr id="5-utilities">
## 5. Utilities - ユーティリティ

> ユーティリティとして使うクラスには `.u-` を付ける。

どんなコンポーネントでも使えるユニバーサルなクラスが必要なことがときにあります。
たとえば、クリアフィックスや縦揃え、テキストの切り詰めなどです。`.-u`というプリフィックスを付与して、これらのクラスがユニバーサルなものだということを明示しましょう。
たとえば、

``` LESS
.u-truncate-text {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
```

すべてのユーティリティはひとつのファイルに書かれるべきです。コンポーネントやmixinsで上書きする必要はありません。

ユーティリティは本当にすこししか必要ありません。
`.u-float-left { float: left; }`というものは必要ありません。コンポーネントで`float: left;`と書けば済みますし、見通しがよいです。

<hr id="6-file-structure">
## 6. File Structure - ファイル構造

ファイルはこのようになるでしょう。

``` LESS
@charset "UTF-8";

@import "normalize.css";

// Variables
@import "media-queries.less";
@import "colors.less";
@import "other-variables-like-fonts.less";

// Mixins
@import "mixins.less";

// Utils
@import "utils.less";

// Components
@import "component-1.less";
@import "component-2.less";
@import "component-3.less";
@import "component-4.less"; // and so forth
```

 [normalize.css](http://necolas.github.io/normalize.css/)をファイルの最初に含めています。ブラウザ間のCSSのデフォルトを標準化します。normalize.cssはすべてのプロジェクトで使用した方がよいです。
 その後、変数やミックスイン、ユーティリティをそれぞれ読み込みます。

そして、コンポーネントを読み込みます。各コンポーネントは、それぞれ独立したフィアルであるべきです。そして各ファイルで必要なモディファイアやステート、メディアクエリを読み込みます。コンポーネントはきっちりと正確に書かれていれば、読み込み順は問題ありません。

そして、`app.css`というファイル（あるいは似た名前のファイル）をひとつで出力するようにするべきです。

<hr id="7-style">
## 7. Style - スタイル

上記のガイドラインに沿っていても、CSSの書き方は千差万別です。一貫した方法でCSSを書くことによって、すべての人が読みやすい状態にすることができます。

``` LESS
.global-header-nav-item {
  background: hsl(0, 0%, 90%);
  border-radius: 3px;
  display: block;
  float: left;
  padding: 8px 12px;
  -webkit-transition: background 100ms;
  transition: background 100ms;
}
```

これは次のルールにしたがっています。

- セレクタ毎と宣言（プロパティと値）毎に改行する。
- ルール間は2つのあたらしい改行を入れる。
- プロパティと値の間にスペースをひとつ入れる。（正） `prop: value;` / （誤）`prop:value;`
- プロパティはアルファベット順で並べる。
- インデントはスペース2つ。4つでもなく、ハードタブでもなく。
- セレクタにはアンダースコアを使わない。キャメルケースも使用しない。
- 適切に短い書き方をする。 （正）`padding: 15px 0;` / （誤）`padding: 15px 0px 15px 0px;`.
- ベンダープリフィックスが必要な機能を使うときは、標準的な書き方を最後にする。（例）`-webkit-transition: all 100ms; transition: all 100ms;` ※ ブラウザは最適化するのだが、古いブラウザの互換性を維持するためにもやった方がよい。標準的な宣言はベンダープレフィックス付きの宣言の後に書くというのは、一番最適化されです。
- ~~hexやrgb(a)といった書き方よりもhslの書き方が好ましい。色の指定はhslでやるのが一番かんたんで、とりわけそれは明るくするときや暗くするときに実感できる。なぜならそういった場合に変更する値がひとつだけで済むからです。~~

Editorconfigやcsscombといったツールで上記を実現することの補佐ができます。

<hr id="8-miscellany">
## 8. Miscellany - その他

このガイドを読んで、私たちのサービスのCSSはさぞかし非の打ち所のないものだろうと思ったかもしれません。そんなことはありません。.jsクラスの規則に則り、namespaced-component-lookingクラスを使っていますが、スタイルやパターンのごちゃまぜが存在します。それでよいのです。プロジェクトが進めていくうちに、規則に従いながらパーツを書き換えるべきです。一度、作業をした場所は、手をつける前よりもよいものにしましょう。

他に心得ておかないといけないものを箇条書きにしておきます。

- コメントは攻撃的にならないようにしましょう。
- マークアップ上では、次のような順にクラスを書きましょう。`<div class="component mod util state js"></div>`
- Data URIを使って10kb以下の画像を埋め込むことができます。リクエスト数を減らせるのですが、CSSのサイズが膨らむので、ロゴなどといったコモンなものだけにしましょう。
- `component-body`というclassを避けましょう。どうせ滅多に使う必要がありません。必要とあらば、コンポーネントにモディファイアを添える形で実現しましょう。
- はっきりとしたクラス名にしましょう。プリプロセッサの機能でトリッキーに名前をつけたりするのはやめましょう。クラス名で検索したいのにできなくなります。もちろん、`.js-`というプリフィックスさえ検索の対象なのでこれに含まれます。
- もしあなたがセレクタ名がCSSファイルを肥大化していると悩んでいるなら、安心してください。圧縮すれば、この点は議論する価値がありません。

追加で読むならこちらのWeb記事をどうぞ。

- [Medium’s CSS guidelines.](https://gist.github.com/fat/a47b882eb5f84293c4ed) I このガイドラインから多くを ~~パクりま~~ 学びました。
- [“CSS At…” from CSS Tricks](http://css-tricks.com/css/). さまざまな会社でのCSSの実践集です。
- BEM（"block", "element", "modifier"）は、このガイドラインのコンポーネントの考え方に似ている方法論です。次の記事でていねいに説明されています。 [this CSS Wizardry article](http://csswizardry.com/2013/01/mindbemding-getting-your-head-round-bem-syntax/)

<hr id="performance">
### Performance - パフォーマンス

パフォーマンスは、それ専用のガイドを用意してもよいくらい価値があるものでしょう。しかし、今回は大きな2つのコンセプトをお話します。
セレクタ・パフォーマンスとレイアウト/ペイントです。

セレクタの問題については、最近ではよりいっそうささいなことというように思える。しかし、Trelloのような複雑なシングルページアプリケーションでは、かなりの多くのDOM操作をしていて、それが問題になることもあります。
[The CSS Tricks article about selector performance](http://css-tricks.com/efficiently-rendering-css/)では、主要セレクタのたいせつな概念が説明されています。
見たところ、`.component-descendant-descendant div`というようなルールは、実際に読み込みコストが高くなります。なぜなら、_右から左_ にCSSのセレクタは読み込まれるからです。というのは、まず最初にすべて（数千にものぼりうる）divを探して、それから親の要素を探すということです。

[Juriy Zaytsev’s post on CSS profiling](http://perfectionkills.com/profiling-css-for-fun-and-profit-optimization-notes/)は、複雑なアプリにおける、セレクタのマッチング、レイアウト、描画、パース時間について概略を提供しています。ポイントの高いセレクタは大きなアプリケーションにおいては悪だというセオリーを確信できます。CSS Wizardyのハリー・ロバーツは、[CSS selector performance](http://csswizardry.com/2011/09/writing-efficient-css-selectors/)という記事も書いています。

もしコンポーネントをきっちりと使用しているなら、セレクターのパフォーマンスについて悩むべきでないです。パフォーマンスは自然とよい状態になっています。

レイアウトと描画はパフォーマンスに大きなダメージを与えるかもしれません。 text-shadow, box-shadow, border-radius, and animationsといったCSS3の機能には気をつけましょう。 [especially when used together](http://www.html5rocks.com/en/tutorials/speed/css-paint-times/)  
私たちはブログにパフォーマンスについての記事を書きました。[back in January 2014](http://blog.fogcreek.com/we-spent-a-week-making-trello-boards-load-extremely-fast-heres-how-we-did-it/)  
これの大部分は、JavaScriptによってレイアウトに鞭打つことよります。ですが、私たちはボーダー、グラデーション、シャドウといった重いスタイル切り離しました。そして、大きく改善されました。

## 付録
- 用語の説明
翻訳の際に、説明を加えた方がよいと思った単語をピックアップして紹介します。  
選定基準は独断と偏見によるものです。

- コンポーネント - コンポーネントは、直訳すれば構成要素です。ソフトウェアでは、ある機能を持ち単体で独立しているものというような使い方でよく見ます。
- モディファイア  - modifyは修飾するといった意味で、モディファイアは修飾するものといった具合だと思います。装飾を非破壊的に行うためのものといった程度に認識しています。
- ステート - これは『状態』と訳してもよかったのですが、そうするとすこし誤解を生みかねないと思い、あえてステートとカタカナで書きました。  
- カプセル化 - オブジェクト指向おなじみの言葉です。今回は、あるコンポーネントが他のコンポーネントから干渉を受けないようにする程度の認識でいる。
- ユーティリティ - 有益なものといったものが原義ですが、今回の場合は『どこでも使える便利なクラス』といった具合だと思います。
- アーキテクチャ - 設計、仕様、設計思想などといった意味です。CSSに関して言うと、CSS設計といった具合に設計という単語が一番多く使われていますね。
- リーダブル - コードは読みやすい状態であるべきです。今の自分にとってすばらしく見えるのは、『今』『あなたが』書いているからであって、半年後のあなたや他の人がそのコードを見たときに読みやすいかを意識しましょう。 / 参考図書 - [リーダブルコード](https://www.oreilly.co.jp/books/9784873115658/)
- メンテナブル - 維持しやすい、保守しやすいといった意味。随時発生する変更に対応可能なコードを書くように心がけましょう。
- メディアクエリ - CSS3からの機能。詳しくは別の記事にどうぞ。 / [メディアクエリ](https://developer.mozilla.org/ja/docs/Web/Guide/CSS/Media_queries)
- ミックスイン - LESSやSass、StylusといったCSSプリプロセッサに存在する機能。各CSSプリプロセッサにて基本的な機能として存在するので、詳しい説明は書きません。
<span id="glossary-namespace"></span>
- 名前空間 - 一意かつ異なる名前をつけることで衝突が起きないようにする方法・概念です。
- CSSのポイント - idはいくつ、classはいくつ、といった具合にセレクタの優先度にポイントを付けることを指す。
- テンプレート - テンプレートとは文書構造を表すファイルを指します。テンプレートの典型はもちろんHTMLです。
- プレゼンテーション - 見た目のこと。CSSでの装飾を指します。
- hsl - Hue(色相)、saturation(彩度)、lightness(明度)の3つで色を決定する方法。色相環は、一度は見たことあると思います。CSS3からサポートされた形式です。


----

<hr id="cakephp">
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
- 名前を`__construct`で指定する ... gettext形式(`__("hoge")`)が変数定義時に利用できない為。

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


**他の情報をお探しですか？**

- [トップ](./)
- [基本ポリシー](./general.md)
- [開発ガイドライン](./development.md)
- [運用ガイドライン](./operations.md)
- [コーディングガイドライン](./coding.md)
- [プラグイン・ライブラリ](./plugins_libraries.md)
- [構築・運用手順書（マニュアル）](./manuals.md)
- [翻訳手順書](./translation.md)
- [テスト手順書（マニュアル）](./manuals-test.md)
- [使用ツールについて](./tools.md)
- [リサーチ](./research.md)
