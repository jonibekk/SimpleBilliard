# Goalous 2.0
Goalous 2.0のリポジトリです。
旧Goalousとは別に管理しています。[旧Goalousはこちら](https://github.com/IsaoCorp/goalous)
当プロジェクトで管理しているソース、ドキュメント、その他のツールのすべてはここから辿れるようになっています。

## Health
|master|develop|
|:--:|:--:|
|[![Build Status](https://magnum.travis-ci.com/IsaoCorp/goalous2.svg?token=33yEbgmrzpwqFzcbu6xi&branch=master)](https://magnum.travis-ci.com/IsaoCorp/goalous2) [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous2/badge.png?branch=master)](https://coveralls.io/r/IsaoCorp/goalous2?branch=master)   |[![Build Status](https://magnum.travis-ci.com/IsaoCorp/goalous2.svg?token=33yEbgmrzpwqFzcbu6xi&branch=develop)](https://magnum.travis-ci.com/IsaoCorp/goalous2) [![Coverage Status](https://coveralls.io/repos/IsaoCorp/goalous2/badge.png?branch=develop)](https://coveralls.io/r/IsaoCorp/goalous2?branch=develop)   |

## Progress
- [Waffle(Kanban Bord)](https://waffle.io/isaocorp/goalous2) ... Goalousの開発状況はこのかんばんボードで管理しています。

## What's Goalous ?
GoalousはIsao発の「チーム力向上のスパイラルを生み出す」目標達成ツールです。

- 本番環境 -> https://www2.goalous.com
- ステージング環境 -> https://stg2.goalous.com

## Documentation
- 全てのドキュメントはここから辿れるようにしてください。
- はじめてGoalous開発に参加される方はまずこのドキュメントのすべてに目を通してください。
- ここ以外に[Google Docs](https://drive.google.com/a/isao.co.jp/#folders/0B6mjvNcPiJ6PLXBlTUJsZWphMG8)にもあります。(Google Docsは未整理)
- 手っ取り早くソース弄ってみたいという方は[開発環境構築手順](docs/process_docs/StartDevelop.md)を見て環境を作ってください。

### ガイドライン
#### 基本ポリシー
- [チームのポリシー](docs/guidelines/TeamPolicy.md)
- [禁止事項](docs/guidelines/Prohibited.md)
- [ドキュメントについて](docs/guidelines/Documentation.md)

#### 開発ガイドライン
- [開発サイクル](docs/guidelines/DevelopmentCycle.md)
- [アジャイル開発](docs/guidelines/Ajile.md)
- [イテレーションについて](docs/guidelines/Iteration.md)

#### 運用ガイドライン
- [GitHub運用ガイドライン](docs/guidelines/GitHub.md)
- [Waffle運用ガイドライン](docs/guidelines/Waffle.md)

#### コーディングガイド
- [CSSコーディングガイド](docs/guidelines/CSS.md)
- [CakePHPコーディングガイド](docs/guidelines/CodingGuideCakePHP.md)

#### プラグイン・ライブラリ
- [jQuery Plugin](docs/guidelines/jQueryPlugins.md)
- [CakePHP Plugin](docs/guidelines/CodingGuideCakePHP.md)
- [PHPライブラリ](docs/guidelines/LibraryForPHP.md)

### 手順書
- [開発環境構築手順書](docs/process_docs/LocalDevEnv.md)
- [GitHub,Waffle運用手順書](docs/process_docs/OperationGitHubAndWaffle.md)
- [AWS Operation](docs/process_docs/OperationForAWS.md)
- [CakePHP](docs/process_docs/OperationForCakePHP.md)
- [トラブルシュート](docs/process_docs/TroubleShooting.md)

### 仕様書
- [サービス概要](docs/design_specifications/GoalousOverview.md)
- [システム概要](docs/design_specifications/SystemOverview.md)
- [ER図](docs/design_specifications/ERD.md)
- [要件定義](http://bit.ly/1BRIFvJ)
- [見積もり](http://bit.ly/1BRIyjU)

#### 機能一覧
- ゴール
- チーム
- フィード
- [コーチ認定](docs/design_specifications/features/CoachApproval.md)

### Tips
- [GitHub](docs/tips/GitHub.md)
- [Git](docs/tips/Git.md)
- [PhpStorm](docs/tips/PhpStorm.md)
- [Travis](docs/tips/Travis.md)
- [Mac](docs/tips/Mac.md)
- [Windows](docs/tips/Windows.md)

### 調査内容
- [SQL](docs/investigations/SQL.md)

### その他
- [体制図](docs/others/ProjectOrganizationDiagram.md)
- [議事録](docs/others/Minutes.md)
- [気付いた事](docs/others/Suggestions.md)
- [各種チートシート](docs/others/Cheetsheets.md)
- [開発支援ツール](docs/others/SupportDevTools.md)
- [UI手記](docs/others/MemoForUI.md)
- [Mixpanel](docs/others/Mixpanel.md)


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

## Developers
[Goalous Developers](https://github.com/orgs/IsaoCorp/teams/goalous_developers)
