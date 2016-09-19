# Goalous 2.0
Goalous 2.0のリポジトリです。  - [旧Goalousはこちら](https://github.com/IsaoCorp/goalous)
当プロジェクトで管理しているソース、ドキュメント、一切のものはこのページより見つけられます。

## Health

|  | TravisCI | Coveralls | Code Climate |  
|:---|:---|:---|:---|
| master | [![Build Status](https://travis-ci.com/IsaoCorp/goalous.svg?token=33yEbgmrzpwqFzcbu6xi&branch=master)](https://travis-ci.com/IsaoCorp/goalous) | [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous/badge.svg?branch=master&service=github&t=p8yPfl)](https://coveralls.io/github/IsaoCorp/goalous?branch=master) | - |
| develop | [![Build Status](https://travis-ci.com/IsaoCorp/goalous.svg?token=33yEbgmrzpwqFzcbu6xi&branch=develop)](https://travis-ci.com/IsaoCorp/goalous) | [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous/badge.svg?branch=develop&service=github&t=p8yPfl)](https://coveralls.io/github/IsaoCorp/goalous?branch=develop) | [![Code Climate](https://codeclimate.com/repos/53b685e0695680777500d34f/badges/cf08317ef617dba84379/gpa.svg)](https://codeclimate.com/repos/53b685e0695680777500d34f/feed) |

## Progress
- [Waffle(Kanban Bord)](https://waffle.io/isaocorp/goalous) ... Goalousの開発状況はこのかんばんボードで管理しています。

## What's Goalous ?
GoalousはIsao発の「最強にオープンな社内SNS」です。

| name | url | branch | branch protected | build assets by CI | auto deploy | test by CI | assets dir name | Basic auth |
|:---|:---|:---|:---|:---|:---|:---|:---|:---|
| 本番環境 | [www.goalous.com](https://www.goalous.com) | master | ◯ | × | × | ◯ | stage | - |
| ISAO環境 | [isao.goalous.com](https://isao.goalous.com) | master-isao | ◯ | × | × | ◯ | stage-isao | id: isao, pass: Vh6RncG8 (only user registration)|
| ステージング環境 | [stg.goalous.com](https://stg.goalous.com) | stage | ◯ | ◯ | ◯ | ◯ | stage | id: stg, pass: c2WgdYaL |
| ISAOステージング環境 | [stg-isao.goalous.com](https://stg-isao.goalous.com) | stage-isao | ◯ | ◯ | ◯ | ◯ | stage-isao | id: stg-isao, pass: X2hkcfqD |
| ホットフィックス環境 | [hotfix.goalous.com](https://hotfix.goalous.com) | hotfix | × | ◯ | ◯ | ◯ | stage | id: hotfix, pass: yD69KAEt |
| 開発環境 | [dev.goalous.com](https://dev.goalous.com) | develop | × | × | ◯ | ◯ | - | id: dev, pass: a5PxhqtL |
| インド開発環境 | [dev-ind.goalous.com](https://dev-ind.goalous.com) | dev-ind | × | × | ◯ | ◯ | - | id: dev-ind, pass: W4hViXcb |
| その他 | - | other | × | × | - | ◯ | - |

## ドキュメンテーション
- すべてのドキュメントはページからアクセスできるようにしてあります。
- はじめてGoalous開発に参加される方はまずこのドキュメントのすべてに目を通してください。
- いち早く環境構築を済ませたい方は、開発ガイドラインの[開発環境構築手順](docs/guidelines/development.md#setup_stack)の項をご参照ください

より詳しくは、基本ポリシー内の[ドキュメンテーション](docs/guidelines/general.md#documentation)の項目をご参照ください。

### ガイドライン

- [基本ポリシー](docs/guidelines/general.md)
  - [はじめに](docs/guidelines/general.md#intro)
  - [チームのポリシー](docs/guidelines/general.md#team-policiy)
  - [禁止事項](docs/guidelines/general.md#forbidden)
  - [ドキュメントについて](docs/guidelines/general.md#documentation)
  - [チームメンバー以外のプロジェクトへの貢献について](docs/guidelines/general.md#contributing)

- [開発ガイドライン](docs/guidelines/development.md)
  - [開発環境構築手順](docs/guidelines/development.md#setup_stack)
  - [開発フロー](docs/guidelines/development.md#development_flow)
  - [アジャイル開発](docs/guidelines/development.md#agile)
  - [イテレーション](docs/guidelines/development.md#iteration)

- [運用ガイドライン](docs/guidelines/operations.md)
  - [GitHub運用について](docs/guidelines/operations.md#github)
  - [Waffle.ioの運用について](docs/guidelines/operations.md#waffleio)
  - [コードレビューについて](docs/guidelines/operations.md#review)
  - [ブランチ運用について](docs/guidelines/operations.md#operation_branches)
  - [サポート用クエリ集](docs/guidelines/operations.md#operation_queries)

- [コーディングガイドライン](docs/guidelines/coding.md)
  - [CSSコーディングガイドライン](docs/guidelines/coding.md#css)
  - [CakePHPコーディングガイドライン](docs/guidelines/coding.md#cakephp)

- [プラグイン・ライブラリ](docs/guidelines/plugins_libraries.md)
  - [jQuery Libraries](docs/guidelines/plugins_libraries.md#jquery-libs)
  - [CakePhp Plugins](docs/guidelines/plugins_libraries.md#cakephp-plugins)
  - [PHP Libraries](docs/guidelines/plugins_libraries.md#php-plugins)

- [構築・運用手順書（マニュアル）](docs/guidelines/manuals.md)
  - [海外開発拠点用プロキシサーバ運用手順書](docs/guidelines/manuals.md#env4foreigner)
  - [海外開発者向け環境構築手順書](docs/guidelines/manuals.md#proxy4foreigner)
  - [AWSオペレーション](docs/guidelines/manuals.md#aws-operation)
  - [DB接続](docs/guidelines/manuals.md#db-connection)
  - [DBマイグレーション](docs/guidelines/manuals.md#db-migration)
  - [GitHubとWaffleの運用](docs/guidelines/manuals.md#github-waffle)
  - [CSオペレーション](docs/guidelines/manuals.md#cs-operation)
  - [メンテナンスモード](docs/guidelines/manuals.md#maintenance-mode)

- [翻訳手順書](docs/guidelines/translation.md)

- [テスト手順書（マニュアル）](docs/guidelines/manuals-test.md)
  - [負荷試験環境構築手順書](docs/guidelines/manuals-test.md#stress-test#stress)
  - [Seleniumを使用したUI自動テスト](docs/guidelines/manuals-test.md#selenium)
  - [PHP Docからドキュメントを生成するapigen](docs/guidelines/manuals-test.md#api-gen)

### 手順書
- [各種マニュアル](docs/manuals.md)

### 仕様書
- [要件定義 OneNote](http://bit.ly/glsurls)
- [システム概要](docs/design_specifications/SystemOverview.md)
- [ER図](docs/design_specifications/ERD.md)
- [要件定義](http://bit.ly/1TnQZfX) **Last Edit - 2015年7月**
- [API仕様](docs/design_specifications/API.md)

#### 機能一覧
- ゴール
- チーム
- フィード
- [コーチ認定](docs/design_specifications/features/CoachApproval.md)

### Tips

- [使用ツールについて](docs/guidelines/tools.md)
  - [ツール一覧](docs/guidelines/tools.md#tool-list)
  - [ツール各論](docs/guidelines/tools.md#tool-details)

### 調査内容

- [リサーチ](docs/guidelines/research.md)
  - [SQL](docs/guidelines/research.md#sql)

### その他
- [議事録](docs/others/Minutes.md)
- [気付いた事](docs/others/Suggestions.md)
- [UI手記](docs/others/MemoForUI.md)
- [古いブランチの墓場](docs/others/BornyardOfOldBranch.md)

## Members
[Goalous Developers](https://github.com/orgs/IsaoCorp/teams/goalous_developers)

## Commands

- Chefのアップデート

```
sh ./etc/local/update_app.sh
```

- browse-sync  
すべてのファイル変更を検知して自動更新します。

```
browser-sync start --proxy "192.168.50.4" --files "**/*.css, **/*.js, **/*ctp, ***/.php **/*.html"
```

