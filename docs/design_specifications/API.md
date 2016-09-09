# Web API設計
関連Issueは、
https://github.com/IsaoCorp/goalous/issues/4876

## 基本方針
- シンプルで利用しやすいAPI
- Restfulすぎず柔軟
- プラットフォームに依存しない

## リクエストメソッド
リクエストメソッドは適切に使う

GET：データ取得
POST：データ新規登録
PUT：データ更新
DEETE：データ削除

データの一部更新を行うPATCHについては議論があるものの正直PATCHとPUTを使い分けるメリットが無いので、データ更新は全てPUTとする

## URL

- 必ずAPIバージョンをつける 
- URLで何のAPIかがぱっと分かるようにリソースを含めた階層構造とする

### URL基本形

/api/{APIバージョン}/{リソース名(複数形)}/{リソースID}

例.
ユーザー新規登録
POST /api/v1/users
ユーザー取得
GET /api/v1/users/1
ユーザー更新
PUT /api/v1/users/1
ユーザー削除
DELETE /api/v1/users/1

■リソースが複数の単語からなる場合
スネークケースで表現する

例.
/api/v1/post_likes/1
/api/v1/team_members/2

### Restfulについて
Restな設計にはするがRestfulでなくてもよい
その方がAPIがシンプルになるしAPI利用側も楽になる。
特に部分的に更新する場合は顕著

例.ユーザーが退会する場合

・Restfulの場合
URL：PUT /api/v1/users/1
Parameter：{status:delete}

ここでstatusのリクエストパラメータが無くてもサーバー側で更新出来るのであればそもそもパラメータは不要。
その代りURLでどのリソースに対して何を行うか判別できるように変える

・Goalousの場合
URL：PUT /api/v1/users/1/withdraw
Parameter：無し

この方がAPIが何をするのか明確であり、かつ使いやすい

■CakePHPに依存したURL禁止
CakePHPの
ctrl/act/param1/param2
ctrl/act/param1:hoge/param2:fuga
といったURLはAPIにおいて使わない

**※APIが言語・フレームワークに依存するのをなるべく避ける**

## リクエスト&レスポンス

### リクエスト
Content-Type：application/x-www-form-urlencoded

#### リクエストパラメータ
- パラメータ名はスネークケース
- 深い階層になるのを避ける(test[test1][test2][test3]***)

##### GETメソッド
全てURLクエリパラメータで表現
/api/v1/users?last_name=goalous&first_name=tarou


##### GETメソッド以外
jsonではなく平文とする

例.
```
$.ajax({
    type: "POST",
    url: "api/v1/users",
    data: { last_name : "goalous" , first_name:"tarou"}
});
```

### レスポンス
#### APIで扱うHTTPステータスコード
200：正常処理
400：リクエスト不正。バリデーションエラーが主
401：認証失敗
403：アクセス権限、データ操作権限が無い。CSRFエラーもここに含まれる
404：Not found
405：Methodが許可されていない
500：サーバーエラー

#### レスポンスパラメータ
- レスポンスBodyにHTTPステータスコードは含めない
- json形式
- パラメータ名はスネークケース
- API成功と失敗で明確にレスポンスを分ける(HTTPステータスコードが200とそれ以外)

##### GETメソッド
- 実データはdata下に格納
- パラメータに適した型で返す(int, string ,bool)

```
{
	data: { // 実データ
		id: 1
		last_name: "goalous",
		first_name: "tarou",
		active_flg: true
	},
	html: {***} // 実データに基づいて作成したHTML(将来的に廃止)
}
```

##### POSTメソッド
新規登録したリソースIDを返す
例.ユーザー新規登録
```
{
	user_id:1
}
```

##### PUT/DELETEメソッド
レスポンスは無しで良い

##### エラー時
- messageにエラー内容が入る
- バリデーションエラーの場合はvalidation_errorsを含む

■基本
```
{
	message: "ユーザー認証に失敗しました"
}
```

■バリデーションエラーの場合
```
{
	message: "バリデーションに失敗しました",
	validation_errors: {
		first_name: "未入力です",
		last_name: "最大文字数を超えています"
	}	
}
```

Web APIエラー参考：[http://qiita.com/suin/items/f7ac4de914e9f3f35884](http://qiita.com/suin/items/f7ac4de914e9f3f35884)

# Web API 詳細設計
## ページング
- どこからどこまでの情報を取得するか指定するのは、offset,limitではなくcursor,limitでクエリパラメータに指定する
- 取得件数(limit)の指定がない場合、システム側が定めたデフォルトの件数とする
- 取得件数に上限を設け、もし指定した取得件数が上限を超えた場合は強制的にシステム側が定めたデフォルトの件数とする(膨大な件数取得によるサーバー負荷を軽減)
- レスポンスに次のページング用のURLを含める

### リクエスト
#### offsetを使用する問題について
1. パフォーマンスが低い
どこからどこまでの情報を取得するか指定する場合、offset,limitを使うことが多々ある。
しかし実はmysqlにおいてoffset,limitは単純に遅い。
なぜなら○件目から○件を取得しているのではなく、全件取得した後いらない部分を切り捨てているからだ。
したがってデータ件数が多ければ多いほどパフォーマンスは低下する。

2. 更新タイミングによるバグ
○件目からという指定だと情報を重複表示するバグが発生する。
例えばポストを作成した降順に取得して表示する場合
・ユーザーAが1件目〜10件目を表示
・もっとみるで11件目を表示する前に他のユーザーがポストを作成
・ユーザーAがもっとみるで11件目〜20件目を表示

この時作成されたポストが1件目にくる為、表示された11件目は10件目と同じになってしまう。
(これはインフィニットロードのあるある問題)

#### offsetの代わりとなるcursor
cursorとは？
要はどこから取得するのか基準となるユニークID。

↑のポストを更新順に取得する例で言うと
1回目に取得した最後の10件目のユニークIDをcursorとして検索する
```
SELECT * FROM users
WHERE id > (10件目のユニークID)
ORDER BY id DESC
LIMIT 10
```

この検索方法によってパフォーマンスが向上し、重複問題も解決する。

### レスポンス
ページング用のAPIはpagingのレスポンスパラメータを含む。
pagingは次回APIコールに使用するURLを設定(limitは除く)

例.検索条件により検索したデータの取得
API:/api/v1/users?keyword=test&keyword2=test2&limit=10
```
{
	data: {***}, // 実データ
	paging: {
		next:"/api/v1/users?keyword=test&keyword2=test2&cursor=10", // 次回のデータ取得時にコールするAPIはこのURLにlimitをくっつけて使用
		before:"***" // 以前のデータ取得時に使用するURLだが、Goalousではまだ使用未定
	}
}
```

もし次回データが存在しない場合はpaging[next]は空となる。
つまり利用側は与えられたURLを基にAPIコールするだけでよくなり、しかも次回データが存在するか判定するために無駄なAPIを投げなくても済む

## レスポンスするデータの例
### 複数データ+複数データ
```json
{
    data: [{
        id: 1,
        name: "Goal1",
        key_result: [{
            id: 1,
            name: "KR1",
        }, {
            id: 2,
            name: "KR2",
        }]
    }, {
        id: 2,
        name: "Goal2",
        key_result: []
    }]
}
```

### 複数データ+単数データ
```json
{
    data: [{
        id: 1,
        name: "Goal1",
        key_result: {
            id: 1,
            name: "KR1"
        }
    }, {
        id: 2,
        name: "Goal2",
        key_result: {}
    }]
}
```

### 単数データ+単数データ
```json
{
    data: {
        id: 1,
        name: "Goal1",
        key_result: {
            id: 1,
            name: "KR1"
        }
    }
}
```

### バリデーションエラー
```json
{
    message: "バリデーションに失敗しました",
    validation_errors: {
        goal: {
            name: "未入力です"
        },
        key_result: [{
            index: 0,
            name: "未入力です"
        }, {
            index: 3,
            name: "未入力です"
        }]
    }
}
```

## APIのバージョン管理について
すべてのコントローラをバージョン毎に分ける。
### urlとディレクトリの例
- url: /api/v1/
  - dir: /app/Controller/Api/V1/
- url: /api/v2/
  - dir: /app/Controller/Api/V2/

### 親コントローラ
- ApiController


