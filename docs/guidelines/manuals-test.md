## テスト手順書

テストに関してのまとめられたマニュアルです。  

<hr id="stress">

### AWS環境構築手順書 (試験環境含む)

1. AWS management consoleにログイン。
1. リージョンを`アジア・パシフィック(東京)`に変更。

#### ロードバランサ作成
1. メニューから`EC2`->`ロードバランサ`
1. ステップ１   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807370/feb0ef2e-03c5-11e5-9ef2-6932183e7b15.png)   
1. ステップ２   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807402/392b130a-03c6-11e5-8c47-649c743cb7f7.png)   
1. ステップ３   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807411/586575e4-03c6-11e5-8c90-08e5312ca1d4.png)   
1. ステップ４   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807445/b065faf2-03c6-11e5-92db-db6a36a2e187.png)   
1. ステップ５   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807467/d9dfacfc-03c6-11e5-9d57-42ef6e98e25d.png)   
1. ステップ６   
![ec2_management_console](https://cloud.githubusercontent.com/assets/3040037/7807477/fae094f2-03c6-11e5-8f8b-0166716eaade.png)   
1. ステップ７   
  `作成`を押す。  
1. ロードバランサ名を控える。
1. 以上。

#### Route53設定
1. Route53のページに移動。
1.    
![route_53_management_console](https://cloud.githubusercontent.com/assets/3040037/7807540/5e98df72-03c7-11e5-88f5-e8b5a6c346c4.png)   
1.   
![route_53_management_console](https://cloud.githubusercontent.com/assets/3040037/7807550/842cb8f8-03c7-11e5-9191-5bf42a6d9006.png)   
1. `Create Record Set`ボタンを押す。
1.   
![route_53_management_console](https://cloud.githubusercontent.com/assets/3040037/7807608/ed0097c8-03c7-11e5-81e6-ac0b67d99fa8.png)

#### RDS作成
1. スナップショットから復元(rds01-stress-test01)
1.    
![rds_ _aws_console](https://cloud.githubusercontent.com/assets/3040037/7807848/bb721eb4-03c9-11e5-8610-624b1c246035.png)   
1. インスタンス作成完了まで待つ   
1.    
![fullscreen_5_26_15__5_13_pm](https://cloud.githubusercontent.com/assets/3040037/7807999/a7dd0552-03ca-11e5-81d6-ce4cbe068ad6.png)   
1.   
![rds_ _aws_console](https://cloud.githubusercontent.com/assets/3040037/7808081/7b6c5fda-03cb-11e5-99b7-0283fa97678e.png)   
1. `DBインスタンスの変更`を押す。
1. 再起動。   
![fullscreen_5_26_15__5_30_pm](https://cloud.githubusercontent.com/assets/3040037/7808268/f4391010-03cc-11e5-99b3-10d0305a106b.png)   
1. エンドポイントを控える。   
![rds_ _aws_console](https://cloud.githubusercontent.com/assets/3040037/7808435/2937332c-03ce-11e5-9763-2a1812724833.png)   

1. 以上。

#### S3バケット作成
1. S3ページに移動。
1. `バケットを作成`ボタンを押す。
1. 以下の２つのバケットを作成。
 - `goalous-stress-test01-logs`
 - `goalous-stress-test01-assets`
1. バケット名を控えておく。

#### ElastiCacheインスタンス作成
1. ElastiCacheページに移動。
1. `Launch Cache Cluster`ボタンを押す。
1. Redisを選択。   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808593/62db5ea4-03cf-11e5-8c7a-ac1548515805.png)   
1.   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808619/8da22be0-03cf-11e5-9f95-418723151559.png)   
1.   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808760/76135f20-03d0-11e5-942c-1a6f514a318b.png)   
1. Endpointを控える。   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808903/778424ec-03d1-11e5-9bd0-8cac4acec7ed.png)   
![elasticache_management_console](https://cloud.githubusercontent.com/assets/3040037/7808920/8e756c74-03d1-11e5-9df0-d6551aba23fd.png)   


#### Opsworks設定
##### 事前確認(名前は今回の場合です。用途に応じて変わる可能性があるのでちゃんと確認しときましょう。)
- ElastiCache
  `gl-stress-test-cache.xnlqxt.0001.apne1.cache.amazonaws.com`
- S3
  `goalous-stress-test01-assets`
  `goalous-stress-test01-logs`
- RDS
  `gls-rds01-stress-test01.cjoncmeaeph3.ap-northeast-1.rds.amazonaws.com`
- ELB
  `gls-stress-test01`

##### 作業
1. Opsworksのページに移動。
1. `Goalous Production`を`clone`する。   
![fullscreen_5_26_15__4_28_pm](https://cloud.githubusercontent.com/assets/3040037/7807201/5ea62018-03c4-11e5-8166-d26c2917fd01.png)   
1. 名前を変更して`Advanced`を押す。   
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7809438/0b705ce0-03d5-11e5-9cd2-0567d6dd4e20.png)   
1. Custom Json を変更する。  
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7821044/16cf7efc-0427-11e5-9d32-309ae3b35031.png)  
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7821048/1b600e50-0427-11e5-9695-7dbe56d6f4db.png)  
1. `Clone Stack`ボタンを押す。  
![clone_stack_01_goalous_production_copy_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7809668/f0f7f740-03d6-11e5-8884-5aebff652656.png)   
1. ELBを紐付ける。   
![goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7809735/5b34d790-03d7-11e5-8699-4ea5c0fad6c9.png)   
1.   
![edit_goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810081/2162c7f4-03da-11e5-83a2-f4d6fe83af32.png)   
1. レイヤ設定   
![goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810144/8d61577c-03da-11e5-866d-c696758b24f0.png)   
![edit_goalous_app_-_layers_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810174/b94d46de-03da-11e5-9ece-ab56db3d5050.png)   
1. インスタンスを追加。   
![instances_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810101/43d2def0-03da-11e5-9949-62ba530e6049.png)   
![instances_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810195/efd6579a-03da-11e5-8416-f3f99be9f978.png)   
1. インスタンス起動(15分くらいかかります)   
![instances_-_90_goalous_stress_test_ _aws_opsworks](https://cloud.githubusercontent.com/assets/3040037/7810206/0ba118a2-03db-11e5-8c14-229292dd9380.png)   
1. 起動したら、以下にアクセスしてひと通り動けばOK   
   `https://stress-test01.goalous.com`
1. 以上

<hr id="selenium">

### Seleinumを使用したUI自動テスト

#### 開発マシンにSeleniumServerをインストール
HomeBrewでインストールできます。
```
$ brew install selenium-server-standalone
```
もしくは下記リンクからダウンロードする。
http://docs.seleniumhq.org/download/

#### 各種ブラウザのドライバをインストール
SeleniumServer標準だとFirefoxとSafariには対応済み。IEとChromeを動かすにはドライバが必要です。

http://docs.seleniumhq.org/download/
このページの中にある**The Internet Explorer Driver Server** というのと
サードパーティの**ChromeDriver**をダウンロード。

 - `/usr/local/Cellar/selenium-server-standalone/2.41.0/bin` にchromedriver を配置。
   ` 2.41.0 `はバージョンなので適宜読み替えて頂ければと思います。
 - それ以外はjarファイルと同じディレクトリ

イメージ
![image](https://cloud.githubusercontent.com/assets/16809401/13978680/60764978-f116-11e5-84a4-e2f6f3293f5e.png)

## SeleniumServerの実行
### jarをダウンロードした場合
```
$ java -jar selenium-server-standalone-2.41.0.jar
```

### HomeBrewで入れた場合
```
$ selenium-server -p 4444
```
chrome driver拡張を有効にするには以下です
```
$ selenium-server -p 4444  -Dwebdriver.chrome.driver=/usr/local/Cellar/selenium-server-standalone/2.48.2/bin/chromedriver
```
オプションとして``` -Dwebdriver.chrome.driver=<chromedriverのパス>```を入力します。
IE driverは```-Dwebdriver.ie.driver=<IEdriverのパス>```を入力します。

特にエラーがなければ、Selenium Serverが起動しています。

イメージ
![image](https://cloud.githubusercontent.com/assets/16809401/13979034/e0d07240-f118-11e5-8cbf-30f7bc34c085.png)
` Selenium Server is up and running `と出ていればOKです。

### PHPUnitを実行する
`  app `に移動して

```
./Console/cake test app <テストファイル名|testsuiteファイル名>

ex)
//全UIテスト実行
./Console/cake test app AllWebTestsTest.php

//メッセージテストのみ実行
./Console/cake test app Web/MessageWebTest.php

//サークルテストのtestDispCircleModalのみ実行
./Console/cake test app --filter="testDispCircleModal" Web/CircleWebTest.php

```
で実行できます。
まだ``` $ composer update ```していなければ実行して頂く必要があります。


#### どうしてもErrorばかりになる場合
環境構築直後のデータでテストが動作することを確認しています。
他に方法がない場合には、覚悟を決めて ``` $ vagrant destroy;vagrant up ``` をお試しください。

**vagrant環境の全データが初期化されます**



#### 参考ソース
 - http://tech.basicinc.jp/Selenium/2014/05/05/selenium2_phpunit/
 - http://blog.htmlhifive.com/2014/10/30/chrome-browser-testing-using-selenium-and-vagrant/
 - http://www.engineyard.co.jp/blog/2014/testing-php-app-with-selenium-on-travis/
 - https://colo-ri.jp/develop/2008/02/selenium_rc_selenium-serverjar.html
 - http://gongo.hatenablog.com/entry/2014/10/29/105755
 - https://phpunit.de/manual/4.8/ja/selenium.html

<hr id="api-gen">

### PHP Docからドキュメントを生成するapigen

phpdocを元にドキュメントを生成してくれるツールです。

イメージ
![image](https://cloud.githubusercontent.com/assets/16809401/13979270/8ec9830e-f11a-11e5-8954-3c7a4bbbc3d8.png)

## インストール
composerでインストールします。すでに` composer.json `に記述してあります。
```
$ composer update
```

## 使用方法

例えばプロジェクトルートにいる場合は下記のコマンドを叩きます。
```
./Vendor/bin/apigen generate -s app/Test/ -d app/webroot/api/
```

` -s ` オプションで対象ソースを指定します。
` -d ` オプションで出力先を指定します。

対象ソースがディレクトリの場合、再帰的に処理してくれるので
上記コマンドを叩いた場合、`app/Test/ `の全てのソースが対象です。

` -d ` はディレクトリを指定します。
上記コマンドを実行した場合、``` app/webroot/api ``` にHTMLファイルが生成されます。
ブラウザで` http://192.168.50.4/api/index.html `にアクセスすると見ることができます。

公式: http://www.apigen.org/







# Windows環境の構築
modernie_selenium(https://github.com/conceptsandtraining/modernie_selenium)を利用
ネットワークの構築、パス指定、Makefileをカスタマイズ
VirtualBox4.3.x系で動作
VirtualBox5.0.x系では動作しない
 - ``` VBoxManage guestcontroll ```の``` copyto ``` ``` copyfrom ```が壊れているため
 - 問題のチケット:https://www.virtualbox.org/ticket/14336

## 実行方法
` app/Test/Case/Web/provision/modernie_selenium `に移動
` $ make fetch ` # **ツール類のダウンロード**
` $ make fetch_vm_win8_ie10 ` # **仮想マシンのダウンロード**
` $ /bin/bash mkvm.sh VMs/IE10.Win8/IE10\ -\ Win8.ova ` # **仮想マシンの作成**

### ツール類のダウンロード。

```
$ make fetch
```

ダウンロードするツール類
- selenium-server-standalone.jar
- deuac.iso
- IEDriverServer.exe
- chromedrive.exe
- jre-windows-i586.exe
- chrome.exe
- firefox.exe

### 仮想マシンのダウンロード
2通りあります。
```
$ make fetch_vms
```
Windows仮想マシンを一括してダウンロードします。

```
$ make fetch_vm_{osのバージョン}_{IEのバージョン}
```

Windows仮想マシンを指定してダウンロードします。
例えば、` $ make fetch_vm_win8_ie10 ` はWindows8のIE10がインストールされているVMです。
※詳細はMakefile参照
仮想マシンは3G〜5Gあるので後者をおすすめします。

### 仮想マシンの作成
下記でVirtualBox上にWindowsマシンを構築します。

```
$ /bin/bash mkvm.sh path/to/*.ova
```

例えば、Windows8のIE10の仮想マシンであれば下記のようになります。

```
$ /bin/bash mkvm.sh VMs/IE10.Win8/IE10\ -\ Win8.ova
```

エラーが起きなければ完了です。
プロビジョニング直後、VMはヘッドレスで動いているので、
VirtualBoxから電源OFF->起動でGUI操作できます。

### TODO
 - vagrant環境->VM(Windows)間でSeleniumを動作させる
 - VM環境の言語、タイムゾーンの設定を追加する

 ---

 次のドキュメントへ進んでください。  

 **リンク貼る予定です**

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
