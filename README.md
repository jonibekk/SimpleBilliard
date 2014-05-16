# Goalous 2.0
Goalous 2.0のリポジトリです。  
旧Goalousとは別に管理しています。[旧Goalousはこちら](https://github.com/IsaoCorp/goalous)  
当プロジェクトで管理しているソース、ドキュメント、その他のツールのすべてはここから辿れるようになっています。  
## Health
- TravisCI
 - master [![Build Status](https://magnum.travis-ci.com/IsaoCorp/goalous2.svg?token=33yEbgmrzpwqFzcbu6xi&branch=master)](https://magnum.travis-ci.com/IsaoCorp/goalous2)
 - develop [![Build Status](https://magnum.travis-ci.com/IsaoCorp/goalous2.svg?token=33yEbgmrzpwqFzcbu6xi&branch=develop)](https://magnum.travis-ci.com/IsaoCorp/goalous2)
- Coveralls
 - master [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous2/badge.png?branch=master)](https://coveralls.io/r/IsaoCorp/goalous2?branch=master)
 - develop [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous2/badge.png?branch=develop)](https://coveralls.io/r/IsaoCorp/goalous2?branch=develop)

## What's Goalous ?
GoalousはIsao発の「チーム力向上のスパイラルを生み出す」目標達成ツールです。

- 本番環境 -> https://www2.goalous.com
- ステージング環境 -> https://stg2.goalous.com

# Development
Goalousの開発に関して。  
Goalousで素早く開発を始められるよう心がけております。
## Requirements
- [Virtual Box](https://www.virtualbox.org/wiki/Downloads) `version >= 4.3.10`
- [Vagrant](http://www.vagrantup.com/downloads.html) `version >= 1.5.0`
- [Git](http://git-scm.com/downloads) `version >= 1.8.5`
- [Chef Client](http://www.getchef.com/chef/install/) `version >= 11.4`

## Installation
1. ソースファイルをClone  
ターミナルを起動し、以下を実行  
`git clone --recursive git@github.com:IsaoCorp/goalous2.git`  
1. vagrantを起動  
ターミナルで以下を実行  
`cd goalous2`  
`vagrant up`  
1. 動作確認  
ブラウザから以下にアクセス  
`http://127.0.0.1:8081`  

## Contributing
みなさんの貢献は大いに歓迎します。  
Goalousを共に改善していきましょう。  
Goalousに貢献したいという素晴らしいマインドを持っている方は以下の手順で開発したものを送って下さい。

1. Forkする  
Githubページ上右上の「Fork」をクリックしてリポジトリをコピーします。
1. ForkしたリポジトリをCloneする  
1. 作業ブランチを切る  
1. コミットする
1. pushする
1. Pull Requestする(マージ対象はdevelop)

# Our Team
Goalousチームについて
## Member
- [菊池厚平](https://github.com/Ko-hei)
- [平形大樹](https://github.com/bigplants)
- [西田昂弘](https://github.com/nishiii)

# Documentation
Goalous開発におけるすべてのドキュメントは[ここ](https://drive.google.com/a/isao.co.jp/#folders/0B6mjvNcPiJ6PLXBlTUJsZWphMG8)にあります。

## Development
- [開発フロー](https://www.lucidchart.com/documents/edit/ae4a8af6-88c8-41fe-a67b-e121f973026b)

## Design Documentation
- [ER図](https://www.lucidchart.com/documents/edit/4f5b2ed4-5153-79ec-ba7f-70600a004117/0)
- [DBスキーマ設計書](https://docs.google.com/a/isao.co.jp/spreadsheets/d/156jnN_MQ9FRyVGRgTKtQd0GiAQ1frN_7JoVF6TqmDRg/edit?usp=sharing)

## Operations
## Infrastructure
- [全体像](https://www.lucidchart.com/documents/edit/4b328b80-5327-fa30-8c2f-0aab0a00da8d)

# Cloud Tools For Project
Goalousプロジェクトで利用しているクラウドツールについて。
## Development
- [TravisCI (テストツール)](https://magnum.travis-ci.com/IsaoCorp/goalous2)
- [Coveralls (カバレッジ分析)](https://coveralls.io/r/IsaoCorp/goalous2)

## Operations
- [AWS Management Console (AWSのリソースの全てを管理)](https://console.aws.amazon.com/console/home?#)

## Metrics
- [Mixpanel (ユーザ行動分析)](https://mixpanel.com/report/388879/events/#events)
- [NewRelic (パフォーマンス監視)](https://rpm.newrelic.com/accounts/652568/applications/3337537)
- [Visual Website Optimizer](http://v2.visualwebsiteoptimizer.com/) (A/Bテスト)
- [Google Analytics](https://www.google.com/analytics/web/?hl=ja&pli=1#report/visitors-overview/a37579734w69803133p83571333/) (アクセス解析)
- [User Voice (ユーザフォーラム/サポート)](http://app.uservoice.com/signin)
- SurveryMonkey (アンケート)

## Communication
- [HipChat (チャットツール)](https://isao.hipchat.com/chat)

## Other
- [Zapier (API連携)](https://zapier.com/app/dashboard)
