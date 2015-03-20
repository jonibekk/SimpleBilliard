## 開発(チームメンバー)
### 通常時
1. 作業を開始する前に`ToDo`から`In Progress`にカードを移動する。
1. `develop`ブランチからブランチを作成し、チェックアウト。
1. コーディング
1. git add .
1. git commit
1. git push
1. PR発行(develop <- 作業したブランチ)
1. 作業が全て終わるまで、コーディング〜`git push`。
1. メンバーにレビュー依頼(Issueのコメントでmention`@IsaoCorp/goalous_developers`を付ける)。
1. カードを`Review`に移動。

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
1. メンバーにレビュー依頼(Issueのコメントでmention`@IsaoCorp/goalous_developers`を付ける)。
1. カードを`Review`に移動。

## Deploy(プロダクトオーナー)
実行前に、`travis`, `coveralls`のエラーが無い事を確認する。

### ステージング
1. `Ready`にあるカードのPRを確認の上、PRマージ(`develop` <- `xxx`)。(約6分〜10分でdeploy完了)
  もし、複数PRが存在するIssueの場合は、マージ後に`In Progress`に戻す。
1. ステージング環境で動作確認。
1. IssueをCloseする。
1. 以上。

### 本番
1. 本番deploy用のPR発行(`master` <- `develop`)
1. deployされる内容を確認の上、PRマージ。
1. 本番環境で動作確認。
1. 以上。

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
