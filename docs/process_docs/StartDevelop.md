## Introduction
Goalousで素早く開発を始められるよう心がけております。
より効率的な手順もしくは運用方法があれば、どんどん修正していきます。

## Requirements
以下のツールはローカルにおける開発時に必須である為、必ずインストールしてください。

- Virtual Box `version >= 4.3.10`
- Vagrant `version >= 1.5.0`
- Git `version >= 1.8.5`
- Chef Client `version >= 11.4`

[インストール手順(windows)](http://bit.ly/1TnOXfz)
[インストール手順(mac)](http://bit.ly/1TnOUAz)

## Recommend
- [hubコマンド](http://qiita.com/yaotti/items/a4a7f3f9a38d7d3415e3)（mac,linuxのみ）

## IDE
当リポジトリは[Phpstorm](http://www.jetbrains.com/phpstorm/)に最適化されています。なるべくこれを使う事を推奨します。そうすれば開発メンバー全員がよりハッピーになれます。

## Installation
1. ソースファイルをClone
ターミナルを起動し、以下を実行
`git clone --recursive git@github.com:IsaoCorp/goalous2.git`
1. vagrantを起動
ターミナルで以下を実行
`cd goalous2`
`vagrant up`
[vagrant upで先に進まない、もしくはエラーが出た場合の対処方法](http://bit.ly/1TnOYjQ)
1. 動作確認
ブラウザから以下にアクセス
`http://192.168.50.4`
