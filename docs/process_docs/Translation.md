# 事前準備

1. 翻訳用のissue作成
1. `vagrant up default` でvmを起動
1. `vagrant provision default` でvmをアップデート
1. `vagrant ssh default` でvmにログイン
1. `/vagrant_data/app/Console/cake i18n extract` を実行
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
1. コミット。(*.moファイルは不要なファイルなので削除してからコミット)
1. プルリクを発行する(親issueに紐付ける)。
1. プルリクのDescriptionに修正対象ファイルと翻訳手順を記載。
1. 担当の方をアサインし、連絡。

# 依頼内容(以下のMarkdownをコピってください。翻訳対象ファイルのファイルパスのリンクは必ず書き換えてください。)

## 翻訳対象ファイル
- [ ] default.po  
https://github.com/IsaoCorp/goalous2/blob/BranchName/app/Locale/jpn/LC_MESAGES/default.po

## 翻訳の手順

1. 上記のリンクをクリック。
1. 編集ボタンをクリック。  
![goalous2_exception_po_at_topic-translate-english_ _isaocorp_goalous2](https://cloud.githubusercontent.com/assets/3040037/5136256/2306cf10-7168-11e4-80cb-9686fadab037.png)
1. ファイルを編集。(英語→日本語の翻訳をする)  
![editing_goalous2_exception_po_at_topic-translate-english_ _isaocorp_goalous2](https://cloud.githubusercontent.com/assets/3040037/5136289/d95a89b4-7168-11e4-8169-ba5218d3bc2d.png)
1. 変更内容を記載し、「commit changes」ボタンを押す。  
![editing_goalous2_exception_po_at_topic-translate-english_ _isaocorp_goalous2](https://cloud.githubusercontent.com/assets/3040037/5136273/7528bd44-7168-11e4-8541-9ff3a8141e9d.png)
1. 以上

## 補足
- Commit Changesを押さないでページを閉じた場合、変更内容が破棄されるので気をつけてください。
- %sなどの記号はシステムで置き換えられる部分なので翻訳後の文章にも入れておいてください。
- 翻訳前の文章が記号のみの場合は、なにも入れなくて大丈夫です。
