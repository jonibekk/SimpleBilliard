# Goalous 2.0
Goalous 2.0のリポジトリです。
旧Goalousとは別に管理しています。[旧Goalousはこちら](https://github.com/IsaoCorp/goalous)
当プロジェクトで管理しているソース、ドキュメント、その他のツールのすべてはここから辿れるようになっています。

## Health
|master|develop|
|:--|:--|
|[![Build Status](https://magnum.travis-ci.com/IsaoCorp/goalous2.svg?token=33yEbgmrzpwqFzcbu6xi&branch=master)](https://magnum.travis-ci.com/IsaoCorp/goalous2) <br> [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous2/badge.svg?branch=master&service=github&t=1Y8INm)](https://coveralls.io/github/IsaoCorp/goalous2?branch=master)  |  [![Build Status](https://magnum.travis-ci.com/IsaoCorp/goalous2.svg?token=33yEbgmrzpwqFzcbu6xi&branch=develop)](https://magnum.travis-ci.com/IsaoCorp/goalous2) <br> [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous2/badge.svg?branch=develop&service=github&t=1Y8INm)](https://coveralls.io/github/IsaoCorp/goalous2?branch=develop) <br> [![Code Climate](https://codeclimate.com/repos/53b685e0695680777500d34f/badges/cf08317ef617dba84379/gpa.svg)](https://codeclimate.com/repos/53b685e0695680777500d34f/feed)  |

## Progress
- [Waffle(Kanban Bord)](https://waffle.io/isaocorp/goalous2) ... Goalousの開発状況はこのかんばんボードで管理しています。

## What's Goalous ?
GoalousはIsao発の「最強にオープンな社内SNS」です。

- [本番環境](https://www.goalous.com)
- [リリース環境](https://isao.goalous.com)
- [ステージング環境](https://stg2.goalous.com)
- [ホットフィックス](https://hotfix2.goalous.com)

## ドキュメンテーション
- 全てのドキュメントはここから辿れるようにしてください。
- はじめてGoalous開発に参加される方はまずこのドキュメントのすべてに目を通してください。
- いち早く環境構築を済ませたい方は、開発ガイドラインの[開発環境構築手順](guideline_development.md#setup_stack)の項をご参照ください

より詳しくは、基本ポリシー内の[ドキュメンテーション](guideline_general.md#Documentation)の項目をご参照ください。

### ガイドライン

- [基本ポリシー](guidelines_general.md)
  - [はじめに](guidelines_general.md#intro)
  - [チームのポリシー](guidelines_general.md#team-policiy)
  - [禁止事項](guidelines_general.md#forbidden)
  - [ドキュメントについて](guidelines_general.md#documentation)
  - [チームメンバー以外のプロジェクトへの貢献について](guidelines_general.md#contributing)

- [開発ガイドライン](guidelines_development.md)
  - [開発環境構築手順](guidelines_development.md#setup_stack)
  - [開発フロー](guidelines_development.md#development_flow)
  - [アジャイル開発](guidelines_development.md#agile)
  - [イテレーション](guidelines_development.md#iteration)

- [運用ガイドライン](guidelines_operation.md)
  - [GitHub運用について](docs/guidelines/guidelines_operations.md#github)
  - [Waffle.ioの運用について](docs/guidelines/guidelines_operations.md#waffleio)
  - [コードレビューについて](docs/guidelines/guidelines_operations.md#review)

- [コーディングガイドライン](guidelines_coding.md)
  - [CSSコーディングガイドライン](docs/guidelines/guidelines_coding.md#css)
  - [CakePHPコーディングガイドライン](docs/guidelines/guidelines_coding.md#cakephp)

- [プラグイン・ライブラリ](guidelines_plugins_libraries.md)
  - [jQuery Libraries](docs/guidelines/guidelines_plugins_libraries.md#jquery-libs)
  - [CakePhp Plugins](docs/guidelines/guidelines_plugins_libraries.md#cakephp-plugins)
  - [PHP Libraries](docs/guidelines/guidelines_plugins_libraries.md#php-plugins)

<!-- ToDo ファイル消す
- [jQuery Plugin](docs/guidelines/jQueryPlugins.md)
- [CakePHP Plugin](docs/guidelines/CodingGuideCakePHP.md)
- [PHPライブラリ](docs/guidelines/LibraryForPHP.md)
 -->

#### Seleniumを使用したUI自動テスト
- [UITest](docs/guidelines/UITest.md)

### 手順書
- [開発環境構築手順書](docs/process_docs/LocalDevEnv.md)
- [PhpStorm設定](docs/process_docs/PhpStormSetting.md)
- [GitHub,Waffle運用手順書](docs/process_docs/OperationGitHubAndWaffle.md)
- [負荷試験環境構築手順書](docs/process_docs/StressTest.md)
- [AWS Operation](docs/process_docs/OperationForAWS.md)
- [CakePHP](docs/process_docs/OperationForCakePHP.md)
- [翻訳手順書](docs/process_docs/Translation.md)
- [海外開発拠点用プロキシサーバ運用手順書](docs/process_docs/OperationForDevProxy.md)
- [海外開発者向け環境構築手順書](docs/process_docs/BuildDevEnvForForeigner.md)
- [トラブルシュート](docs/process_docs/TroubleShooting.md)

### 仕様書
- [サービス概要](docs/design_specifications/GoalousOverview.md)
- [システム概要](docs/design_specifications/SystemOverview.md)
- [ER図](docs/design_specifications/ERD.md)
- [要件定義](http://bit.ly/1TnQZfX)

#### 機能一覧
- ゴール
- チーム
- フィード
- [コーチ認定](docs/design_specifications/features/CoachApproval.md)

### Tips

- [使用ツールについて](guidelines_tools.md)

<!--
- [開発支援ツール](docs/others/SupportDevTools.md)
- [GitHub](docs/tips/GitHub.md)
- [Git](docs/tips/Git.md)
- [PhpStorm](docs/tips/PhpStorm.md)
- [Travis](docs/tips/Travis.md)
- [Mixpanel](docs/others/Mixpanel.md)

 -->

[仮想マシンを動かすOSについて](guidelines_os.md)

何も書いてない。

<!--
- [Mac](docs/tips/Mac.md)
- [Windows](docs/tips/Windows.md)
 -->



### 調査内容

- [リサーチ](guidelines_research.md)

<!--
- [SQL](docs/investigations/SQL.md)
 -->

### その他
- [議事録](docs/others/Minutes.md)
- [気付いた事](docs/others/Suggestions.md)
- [UI手記](docs/others/MemoForUI.md)
- [古いブランチの墓場](docs/others/BornyardOfOldBranch.md)

## Members
[Goalous Developers](https://github.com/orgs/IsaoCorp/teams/goalous_developers)

* Kohei Kikuchi(PO)
  * Daiki Hirakata(chief developer)
    * Hyungdoo Kil(developer)
    * Shohei Saeki(developer)
    * Siddharth(developer)
  * Naruhito Kubota(front-end engineer)

 <!-- - [体制図](docs/others/ProjectOrganizationDiagram.md) -->


## Commands
browse-sync  
すべてのファイル変更を検知して自動更新します。

```
browser-sync start --proxy "192.168.50.4" --files "**/*.css, **/*.js, **/*ctp, ***/.php **/*.html"
```
