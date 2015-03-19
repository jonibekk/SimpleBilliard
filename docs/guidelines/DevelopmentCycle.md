## 概要説明
PR制度を採用しています。
各環境(本番、ステージング、ホットフィックス)へのDeployは必ずPRのマージによって行われます。

## 操作手順
ターミナルでコマンドを実行

1. goalousのディレクトリに移動
1. vagrantを起動
`vagrant up`
1. アプリケーションをアップデート
`sh ./etc/local/update_app.sh`
1. 開発環境にログイン
`vagrant ssh`
1. 作業用ブランチを作成
`git branch topic-xxxx`
1. 作業ブランチにチェックアウト
`git checkout topic-xxxx`
1. 作業後にコミット(関連するIssue番号をコミットログにつける。)
`git add .`
`git commit`
1. GitHubにプッシュ
`git push origin topic-xxx`
1. PR発行
descriptionに関連Issueの番号を`Connected to #[Issue番号]`という風に記載すると親IssueとWaffle上で紐付けられる。
1. メンバーにレビュー依頼(Issueのコメントでmention`@IsaoCorp/goalous_developers`を付ける)。
1. 第三者にレビューをしてもらいマージしてもらう
