## 開発環境構築手順
1. `vagrant plugin install vagrant-proxyconf`でvagrantプラグイン(proxy設定を可能にするやつ)をインストール。
1. `vagrant up ec2`でec2インスタンス起動およびchefの適用(30min)
1. AWS Management Consoleにログインし以下を実行
 - EIPを紐付ける
 - Instance Nameを変更
1. `vagrant ssh ec2`でssh接続
1. `vncserver :1`でvncserverを起動  
  パスワードを設定
1. vncserver接続テスト
1. firefox, chromeで以下のサイトにそのユーザのIDでログイン
 - github
 - waffle
 - coveralls
 - travis
 - slack
1. `ssh-keygen`でsshキーを発行
1. githubにsshキーを登録
1. `ssh -T git@github.com`でssh接続確認
1. phpstormのライセンス紐付け

以上。
