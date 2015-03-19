これは以前、Goalousをリニューアルした際に書いたものをそのままコピペしたもの。
修正する必要あり。

# javascript
## 統一するファンクション
- ajax通信（submitイベント）
※ajaxか画面遷移はこの中で切り替える。ajax判定はコントローラで。
- ajaxのリクエスト先はformのactionで指定する（必ずここで指定する！）
- ajaxのコールバック関数
  追加、編集、削除したデータの表示への反映
  保存したデータを現在のフォームへ反映nameで検索し渡す

- フォームコレクタ
- トグル（イベントハンドラにターゲットを特定する属性を持つ gl-toggle-target-class ,gl-obj-name,gl-obj-id）
- ＋－ボタンの制御
- バインダをfunctionに切り出す（要素の塊をcloneする時にその中でバインドするケースが多い為）
- fadeIn,fadeOutなど共通のエフェクトをfunctionで切り出す

## query
jqueryテンプレートをうまく使う


# html
※classで指定する内容

- viewer
- viewable
- addable（formでセットする）
- error-message
- delete-button
- edit-button
- add-button
- progress-bar
- progress-value
- more-view
- [obj-name]-list
- text-change-toggle
- click-toggle
- hover-toggle

## モーダル等のコンポーネント系は全てclassで表示する
ポップアップ等の要素は別で分ける

## 必要な属性
- gl-obj-name （goal,step,action等）
- gl-obj-id
- gl-view-item-name
- gl-field-name 例：Goal.name等


## 属性を設定する要素
- action、goalなどのまとまりをあらわすdiv,li等。

## 必要な要素

- 何かしらアクションが発生する可能性のある要素の中に
- `<div>`でメッセージを表示する空要素を置いておく
- more-view

## view内のphp
foreachで回す場合は一つ空の要素を追加する

# php
- post、ajaxかどうかは同一メソッド内で判定する（ビジネスロジックは一緒だから）
- postの場合は$this->setでviewに結果を渡す
- ajaxの場合は、echo json_encode()で結果を渡す。（フォーマットはアプリ内で共通にする）
※すべてのpublic functionはpost,ajaxの両方に対応できるようにしておく。

- モーダル等の共通のコンポーネントはヘルパーにする？

- foreachで回すときに、要素が一つも無い場合、foreach意外でhtmlを書いておかないとajaxで要素を追加できない問題

- foreachで回す値は、空のレコードを予め挿入しておく。
- display:noneの意味のキーを持たせ、trueにしておく。
- htmlでforeachを回す際、display:noneを判定し、trueならstyle="display:none;"にし、gl-new-itemをtrueにする
