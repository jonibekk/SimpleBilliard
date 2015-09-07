# プロキシサーバ運用手順(海外開発拠点用)

## プロキシサーバへの接続
1. sshキーを追加  
  以下ファイルをダウンロード  
  http://bit.ly/1XzyyqD  
  以下の場所にファイルをコピー  
  ~/.ssh/  
  
1. sshでproxyサーバーに接続  
  sh -i ~/.ssh/isao-gls-singapore.pem ubuntu@52.74.224.129  
  ※もし接続できない場合は、接続IPアドレスが許可されていない可能性があるので、管理者にパブリックIPアドレスを連絡する。

## 許可するドメインを追加する手順
1. ホワイトリストを開く  
  `sudo vim /etc/squid3/whitelist`
1. 末尾に許可するドメインを追加し、ホワイトリストを保存(アクセスログ等を確認して拒否されたドメインを特定する)
1. プロキシサーバのリロード  
  `sudo service squid3 reload`
1. 以上

## アクセスログ確認手順
1. tailで以下ファイルを開く  
  `sudo tail -1000f /var/log/squid3/access.log`  
  ※プロキシサーバに拒否されたドメインを追加する場合は、`TCP_DENIED`のドメインを探す。  
  ```
  ## e.g. 
  1441584136.154      2 10.0.0.194 TCP_DENIED/403 3467 CONNECT mtalk.google.com:5228 - HIER_NONE/- text/html
  ```  
  この場合は、`mtalk.google.com`からのアクセスが拒否されているという事。

