<hr id="github">

githubのそれぞれの機能をどういった定義で使うか決める。

# Readme
### ルール
- 全ての情報に辿れるようにする
- ここではダイジェストを記載し、詳細はwikiに書きそのリンクを配置する

### 言語(jp/en)
 en

# Milestones
### ルール
- 追加する場合はメンバーに共有
- 別管理のWBSに対応するようにする
- 期限を設定し、守る
- 要件を書く

### 粒度
- ユーザインパクトを与える機能

### 言語(jp/en)
#### title
 en
#### description
 jp(そのうち全部enにする)

# Issues
### ルール
- 必ず、マイルストーンに紐付ける。
- 設計はdescriptionに書く。
- タスクリストはdescriptionに書く。

### 粒度
- 2日以内で完了させられるもの。

### 言語(jp/en)
#### title
 en
#### description
 jp(そのうち全部enにする)

# Pull requests
### ルール
バグを残さない。
テストを書く。
リファクタリングする。
コメントを適切にする。
プルリクの手段はweb guiから行う方法と、hubコマンド、phpstormのプルリクエストコマンドのいずれでもよい。
プルリクのdescriptionは書かない。
コメントでレビュー、マージの依頼と最終結果を記載する。

### 言語(jp/en)
#### title
 en
#### description
 jp

# Labels
### ルール
一度、分類に分けて整理する。(コーヘイさんが)
例えば、ブランチの接頭辞を一つの分類とする。
機能ベースのラベルはやめる。
接頭辞は数字にする。

### 言語(jp/en)
 en

# Commit
### ルール
- コミットログ末尾にIssue番号を必ず入れる。
  e.g. `fixed bug of index function #123`
- 複数作業をコミットに含めない。

### 内容
 作業内容をわかりやすく記載。

### 粒度
 １つの作業単位。
### 言語(jp/en)
 en

# Wiki
### 内容
- 開発環境の構築手順
- ガイドライン等
- アーキテクチャなどの全体で共通のドキュメント
- 仕様書

### 言語(jp/en)
#### title
 en
#### description
 jp

<hr id="waffleio">

## Waffle.io運用ガイドライン

Waffleのガイドライン。

# 共通のルール
- 誰がメインに動いたIssueかわからなくならないようにレビュー時の再アサインは行わない。

# カード
## 数字
- 1 -> 1時間
- 2 -> 2時間
- 3 -> 3時間
- 5 -> 5時間
- 8 -> 8時間
- 13 -> 2日
- 20 -> 3日間
- 40 -> 5日間

# カラム
## Icebox
### 概要
まだやるかやらないか決まっていないIssue。
### 入る時
計画外のアイディア、バグ修正が発見
### 出る時
 やるって決まった時。

## Backlog
### 概要
- やるか決まっているIssue。
- ここにカードを入れた時点で作業量の数字をつける。

### 入る時
Iceboxに入っているカードがやるって決まった時。
### 出る時
やるって決まってたけど、Iceboxに戻す時。
今週やるやつ。

## ToDo
### 概要
今週やるIssue。
### 入る時
今週やるって決まったとき。
### 出る時
今週やらなくなったとき。
作業開始したとき。

## In Progress
### 概要
作業中のIssue。
プルリクが完了した場合で、Issueを閉じれない場合は、
Redyに移さない。

### 入る時
作業開始時。
### 出る時
作業完了したとき。
保留にするIssue。Backlogの先頭に配置する。

## Review
### 概要
コードレビュー待ちのやつ。
### 入る時
作業完了したとき。
### 出る時
コードレビュー完了後。

## Ready
### 概要
マージ可能なIssue。
### 入る時
コードレビュー完了したとき。
### 出る時
Issueをクローズしたとき。

## Done
### 概要
完了したIssue。
### 入る時
Issueをクローズしたとき。
### 出る時
Issueを再び開いて作業を続行するとき。

<hr id="review">

# レビュー前チェックリストの運用ガイドライン

## チェックリスト

```
## レビュー前チェックリスト
### 共通(全て必須)
- [ ] 仕様を満たしている
- [ ] PRのマージ先は正しい
- [ ] 更新したファイルはリフォーマットされている
- [ ] 修正したファイルにタイプミスによる意図しない文字が混入していない
- [ ] travisでエラーが出ていない
- [ ] エビデンスを残してある

### クライアントサイド
- [ ] ブラウザのコンソールでエラーが出ていない
- [ ] cssのクラス定義を変更した場合、同じクラスが利用されている箇所で問題が発生していない
- [ ] jsの変数名を変更している場合、同じ変数を利用している箇所に問題が発生していない
- [ ] jsのメソッド名、引数を変更している場合、同じメソッドを利用している箇所に問題が発生していない

### サーバサイド
- [ ] cakeのerrorログ、debugログでエラーが出ていない
- [ ] Modelに修正がある場合、テストコードも修正している
- [ ] coverallsでカバレッジが下がっていない(Modelで単に不要な行を削除した場合を除きます)
- [ ] 変数名を変更している場合、同じ変数を利用している箇所に問題が発生していない
- [ ] メソッド名、引数を変更している場合、同じメソッドを利用している箇所に問題が発生していない
```

## チェックのフロー
1. [作業者] PR発行
1. [作業者] PRのdescriptionにチェックリストを貼り付ける
1. [作業者] 該当する項目を確認の上チェックを入れる(該当しないものはそのままにしておく)
1. [作業者] レビュー依頼
1. [レビュア] コードを確認の上チェックが漏れていないかチェック
1. [レビュア] 問題があれば本人に指摘or確認のコメントを送る
1. [レビュア] 問題無ければその旨のコメントを送る
1. [作業者] PRをマージする
1. [作業者] ステージングで確認(マージ後3,4分でステージング環境に反映される)
1. [作業者] 問題あれば、レビュアにコメントし、修正後に新しいPRを発行し、再度レビュー依頼
1. [作業者] 問題なければissueをReadyへ移動
1. 終了

## 補足事項
- エビデンスはissueのコメントに画像 or gifアニメーションで残し、PRにそのコメントのリンクを貼り付ける
- hotfix等の場合も同様のフロー


## 開発(チームメンバー)
**全面的に書き換えます！！**
### 通常時
1. 作業を開始する前に`ToDo`から`In Progress`にカードを移動する。
1. `develop`ブランチからブランチを作成し、チェックアウト。
1. コーディング
1. git add .
1. git commit
1. git push
1. PR発行(develop <- 作業したブランチ)
1. 作業が全て終わるまで、コーディング〜`git push`。
1. メンバーにレビュー依頼(Issueのコメントでmentionを付ける)。
1. カードを`Review`に移動。

**全面的に書き換えます！！**
### release(リリース前準備)
1. 作業を開始する前に`ToDo`から`In Progress`にカードを移動する。
1. `master`ブランチを更新。
1. `master`ブランチから`release`ブランチを作成する。
1. `release`ブランチからブランチを作成し、チェックアウト。
1. コーディング
1. git add .
1. git commit
1. git push
1. PR発行(`release`<- 作業したブランチ)
1. 作業が全て終わるまで、コーディング〜`git push`。
1. メンバーにレビュー依頼(Issueのコメントでmentionを付ける)。
1. カードを`Review`に移動。

**全面的に書き換えます！！**
### hotfix(緊急時)
1. 作業を開始する前に`ToDo`から`In Progress`にカードを移動する。
1. `master`ブランチを更新。
1. `master`ブランチから`hotfix`ブランチを作成する。
1. `hotfix`ブランチからブランチを作成し、チェックアウト。
1. コーディング
1. git add .
1. git commit
1. git push
1. PR発行(`hotfix`<- 作業したブランチ)
1. 作業が全て終わるまで、コーディング〜`git push`。
1. メンバーにレビュー依頼(Issueのコメントでmentionを付ける)。
1. カードを`Review`に移動。

## Deploy(プロダクトオーナー)
実行前に、`travis`, `coveralls`のエラーが無い事を確認する。

**全面的に書き換えます！！**
### ステージング
1. `Ready`にあるカードのPRを確認の上、PRマージ(`develop` <- `xxx`)。(約6分〜10分でdeploy完了)
  もし、複数PRが存在するIssueの場合は、マージ後に`In Progress`に戻す。
1. ステージング環境で動作確認。
1. IssueをCloseする。
1. 以上。

**全面的に書き換えます！！**
### 本番
1. 本番deploy用のPR発行(`master` <- `release`)
1. deployされる内容を確認の上、PRマージ。
1. 本番環境で動作確認。
1. 以上。

**全面的に書き換えます！！**
### リリース
1. `Ready`にあるカードのPRを確認の上、PRマージ(`release` <- `release-xxx`)。(約6分〜10分でdeploy完了)
  もし、複数PRが存在する、もしくは継続のIssueの場合は、マージ後に`In Progress`に戻す。
1. `release`環境で動作確認。
  もし、複数PRが存在する、もしくは継続のIssueの場合は、以下手順は不要。
1. 本番deploy用のPR発行(`master` <- `release`)
1. deployされる内容を確認の上、PRマージ。
1. 本番環境で動作確認。
1. `develop`に`release`をマージ。
1. `release`ブランチを削除。
1. 以上。


**全面的に書き換えます！！**
### hotfix
1. `Ready`にあるカードのPRを確認の上、PRマージ(`hotfix` <- `hotfix-xxx`)。(約6分〜10分でdeploy完了)
  もし、複数PRが存在する、もしくは継続のIssueの場合は、マージ後に`In Progress`に戻す。
1. `hotfix`環境で動作確認。
  もし、複数PRが存在する、もしくは継続のIssueの場合は、以下手順は不要。
1. 本番deploy用のPR発行(`master` <- `hotfix`)
1. deployされる内容を確認の上、PRマージ。
1. 本番環境で動作確認。
1. `develop`に`hotfix`をマージ。
1. `hotfix`ブランチを削除。
1. 以上。

<hr id="operation_branches">

# ブランチ運用について
- hotfixの場合(本番環境の緊急バグフィックス)
    - hotfix -> hotfix0000-something ブランチ生成し、hotfixにマージ
    - hotfix.goalous.comで動作確認
    - master <- hotfix をマージし、本番環境にdeploy
    - www.goalous.comで動作確認
    - stage <- hotfix をマージ
    - stage-isao <- stage をマージ
    - master-isao <- satge-isao をマージし、ISAO環境にdeploy
    - isao.goalous.comで動作確認
    - develop <- stage をマージ
    - 以上
- stage fixの場合(ステージング環境でのバグフィックス)
    - stage -> stage-fix0000-something ブランチを生成し、stageにマージ
    - stg.goalous.comで動作確認
    - stage-isao <- stage をマージ
    - develop <- stage をマージ
    - 以上
- stage-isao fixの場合(ISAOステージング環境でのバグフィックス、developへのマージはしない)
    - stage-isao -> stage-isao-fix0000-something ブランチを生成し、stage-isaoにマージ
    - stg-isao.goalous.comで動作確認
    - 以上
- master-isao fixの場合(ISAO環境でのバグフィックス、developへのマージはしない)
    - master-isao -> hotfix-isao -> hotfix-isao0000-something ブランチを生成
    - hotfix-isao <- hotfix-isao0000-something をマージ
    - stg-isao.goalous.comのむき先を一時的にhotfix-isaoに変更しdeploy&動作確認
    - master-isao <- hotfix-isaoをマージ
    - stage-isao <- master-isaoをマージ
    - 以上

<hr id="operation_queries">

# サポート用のクエリ集
## ファイル検索
### 投稿、メッセージ
```sql
USE isao_goalous;#本番はwww_goalous,ISAOはisao_goalous
SET @keyword = "hogehoge";

SELECT DISTINCT(p.id) as post_id,
  p.type
FROM attached_files af, comment_files cf, comments c, posts p
WHERE af.attached_file_name LIKE concat("%",@keyword,"%") AND af.id = cf.attached_file_id AND cf.comment_id = c.id AND c.post_id = p.id
```

---

次のドキュメントへ進んでください。  
[コーディングガイドライン](./coding.md)

トップへ戻りますか？  
[GitHub - Goalous](https://github.com/IsaoCorp/goalous2)

----

**他の情報をお探しですか？**

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
