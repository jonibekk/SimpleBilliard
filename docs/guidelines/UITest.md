# SeleniumTestに必要な環境構築

## 開発マシンにSeleniumServerをインストール
HomeBrewでインストールできます。
```
$ brew install selenium-server-standalone
```
もしくは下記リンクからダウンロードする。
http://docs.seleniumhq.org/download/

## 各種ブラウザのドライバをインストール
SeleniumServer標準だとFirefoxとSafariには対応済み。IEとChromeを動かすにはドライバが必要です。

http://docs.seleniumhq.org/download/
このページの中にある**The Internet Explorer Driver Server** というのと
サードパーティの**ChromeDriver**をダウンロード。

 - ``` /usr/local/Cellar/selenium-server-standalone/2.41.0/bin ```にchromedriver を配置。
   ``` 2.41.0 ```はバージョンなので適宜読み替えて頂ければと思います。
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
``` Selenium Server is up and running ```と出ていればOKです。
### PHPUnitを実行する
```  app ```に移動して

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


## どうしてもErrorばかりになる場合
環境構築直後のデータでテストが動作することを確認しています。
他に方法がない場合には、覚悟を決めて ``` $ vagrant destroy;vagrant up ``` をお試しください。

**vagrant環境の全データが初期化されます**



## 参考ソース
 - http://tech.basicinc.jp/Selenium/2014/05/05/selenium2_phpunit/
 - http://blog.htmlhifive.com/2014/10/30/chrome-browser-testing-using-selenium-and-vagrant/
 - http://www.engineyard.co.jp/blog/2014/testing-php-app-with-selenium-on-travis/
 - https://colo-ri.jp/develop/2008/02/selenium_rc_selenium-serverjar.html
 - http://gongo.hatenablog.com/entry/2014/10/29/105755
 - https://phpunit.de/manual/4.8/ja/selenium.html
 

 







# Document生成ツールapigenについて
phpdocを元にドキュメントを生成してくれるツールです。

イメージ
![image](https://cloud.githubusercontent.com/assets/16809401/13979270/8ec9830e-f11a-11e5-8954-3c7a4bbbc3d8.png)

## インストール
composerでインストールします。すでに``` composer.json ```に記述してあります。
``` $ composer update ```

## 使用方法
例えばプロジェクトルートにいる場合は下記のコマンドを叩きます。
```
./Vendor/bin/apigen generate -s app/Test/ -d app/webroot/api/
```
``` -s ``` オプションで対象ソースを指定します。
``` -d ``` オプションで出力先を指定します。

対象ソースがディレクトリの場合、再帰的に処理してくれるので
上記コマンドを叩いた場合、``` app/Test/ ```配下の全てのソースが対象です。

``` -d ``` はディレクトリを指定します。
上記コマンドを実行した場合、``` app/webroot/api ``` にHTMLファイルが生成されます。
ブラウザで``` http://192.168.50.4/api/index.html ```にアクセスすると見ることができます。

公式: http://www.apigen.org/







# Windows環境の構築
modernie_selenium(https://github.com/conceptsandtraining/modernie_selenium)を利用
ネットワークの構築、パス指定、Makefileをカスタマイズ
VirtualBox4.3.x系で動作
VirtualBox5.0.x系では動作しない
 - ``` VBoxManage guestcontroll ```の``` copyto ``` ``` copyfrom ```が壊れているため
 - 問題のチケット:https://www.virtualbox.org/ticket/14336

## 実行方法
``` app/Test/Case/Web/provision/modernie_selenium ```に移動
``` $ make fetch ``` # **ツール類のダウンロード**
``` $ make fetch_vm_win8_ie10 ``` # **仮想マシンのダウンロード**
``` $ /bin/bash mkvm.sh VMs/IE10.Win8/IE10\ -\ Win8.ova ``` # **仮想マシンの作成**

### ツール類のダウンロード。

``` $ make fetch ```
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
``` $ make fetch_vms ```
Windows仮想マシンを一括してダウンロードします。

``` $ make fetch_vm_{osのバージョン}_{IEのバージョン} ```
Windows仮想マシンを指定してダウンロードします。
例えば、``` $ make fetch_vm_win8_ie10 ```はWindows8のIE10がインストールされているVMです。
※詳細はMakefile参照
仮想マシンは3G〜5Gあるので後者をおすすめします。

### 仮想マシンの作成
下記でVirtualBox上にWindowsマシンを構築します。
``` $ /bin/bash mkvm.sh path/to/*.ova ```

例えば、Windows8のIE10の仮想マシンであれば下記のようになります。
``` $ /bin/bash mkvm.sh VMs/IE10.Win8/IE10\ -\ Win8.ova ```

エラーが起きなければ完了です。
プロビジョニング直後、VMはヘッドレスで動いているので、
VirtualBoxから電源OFF->起動でGUI操作できます。







# TODO
 - vagrant環境->VM(Windows)間でSeleniumを動作させる
 - VM環境の言語、タイムゾーンの設定を追加する
