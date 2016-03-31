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
