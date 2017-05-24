# 事前準備

1. 翻訳用のissue作成
1. `vagrant up default` でvmを起動
1. `vagrant provision default` でvmをアップデート
1. `vagrant ssh default` でvmにログイン
1. `Console/cake i18n extract --no-location --ignore-model-validation —overwrite` を実行
1. 以下の通り応対

```
  Current paths: None
  What is the path you would like to extract?
  [Q]uit [D]one  
  [/vagrant_data/app/] > [Enter]

  Current paths: /vagrant_data/app/
  What is the path you would like to extract?
  [Q]uit [D]one  
  [D] > [Enter]

  Would you like to extract the messages from the CakePHP core? (y/n)
  [n] > [Enter]
  What is the path you would like to output?
  [Q]uit  
  [/vagrant_data/app/Locale] > [Enter]

  Would you like to merge all domain and category strings into the default.pot file? (y/n)
  [n] > [Enter]

  Error: global.pot already exists in this location. Overwrite? [Y]es, [N]o, [A]ll (y/n/a)
  [y] > a
```

1. 新しいブランチを`develop`から作成。(developにチェックアウト -> Pullする -> ブランチ作成)
1. poeditを開く。(※インストールされていない場合は、https://poedit.net/download からダウンロード、インストール)
1. `Edit a translation`で該当のファイルを開く。  
  ファイルパス: `goalous2/app/Locale/jpn/LC_MESSAGES/default.po`
1. poeditのメニュー[Catalog]->[Update from POT file]を選択。
1. 対象の`pot`ファイルを指定(拡張子以外が同名)。
   ファイルパス: `goalous2/app/Locale/default.pot`
1. cmd + s で上書き保存。(※エラーが出ても気にしない。)
1. コミット。(`*.mo`ファイルが出現することがあるが、このファイルは不要なので、削除してからコミットする。)
1. プルリクを発行する(親issueに紐付ける)。
1. プルリクのDescriptionに修正対象ファイルと翻訳手順を記載。
1. 担当の方をアサインし、連絡。

# 翻訳手順

## 翻訳対象ファイル
- [ ] default.po  
https://github.com/IsaoCorp/goalous2/blob/BranchName/app/Locale/jpn/LC_MESAGES/default.po

## 翻訳の手順

1. 上記のページにて、編集ボタンをクリック。  
<img width="480" alt="goalous2_default_po_at_update-translation-document__isaocorp_goalous2" src="https://cloud.githubusercontent.com/assets/7731249/16140689/52c15a5a-348e-11e6-8930-97f7dd11d374.png">

1. ファイルを編集。(英語→日本語の翻訳をする)  
<img width="480" alt="goalous2_default_po_at_update-translation-document__isaocorp_goalous2" src="https://cloud.githubusercontent.com/assets/7731249/16140808/81282490-348f-11e6-96c0-f3952a2f9d7e.png">

1. 変更内容を記載し、「commit changes」ボタンを押す。  
<img src="https://cloud.githubusercontent.com/assets/3040037/5136273/7528bd44-7168-11e4-8541-9ff3a8141e9d.png" alt="editing_goalous2_exception_po_at_topic-translate-english__isaocorp_goalous2" width="480">  
**ここを忘れると変更内容は消えるので注意**

以上

### 補足
- %sなどの記号はシステムで置き換えられる部分なので翻訳後の文章にも入れておいてください。
- 翻訳前の文章が記号のみの場合は、なにも入れなくて大丈夫です。


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
