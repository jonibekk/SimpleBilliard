# 事前準備

1. 翻訳用のissue作成
1. PR作成(親issue)に紐付け
1. vagrant ssh でvmにログイン
1. `./Console/cake i18n extract` を実行
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

1. 一旦コミットする。
1. poeditを開く。(※インストールされていない場合は、https://poedit.net/download からダウンロード、インストール)
1. `Edit a translation`で該当ファイルを開く。
  ![open_and_poedit_and_editing_goalous2_translation_md_at_doc-translate_ _isaocorp_goalous2_and_authy](https://cloud.githubusercontent.com/assets/3040037/7676825/28f040ae-fd82-11e4-941f-28d3d17b582a.png)   
1. poeditのメニュー[Catalog]->[Update from POT file]を選択。
1. 対象の`pot`ファイルを指定(拡張子以外が同名)。   
   ![open](https://cloud.githubusercontent.com/assets/3040037/7676892/b08129de-fd82-11e4-8769-567e049b06ea.png)   
1. cmd + s で上書き保存。(※エラーが出ても気にしない。)
1. 同様の手順で全ファイルを更新。
1. コミット。(*.moファイルは不要なファイルなので削除してからコミット)
1. プルリクを発行する(親issueに紐付ける)。
1. プルリクに修正対象ファイルを指定する。
1. 翻訳手順をDescriptionに記載。
1. 依頼先に連絡。

# 依頼内容(以下をコピってください。ファイルパスのリンクは必ず書き換えてください。)

## 翻訳対象ファイル
- [ ] exception.po  
https://github.com/IsaoCorp/goalous2/blob/topic-translate-english/app/Locale/eng/LC_MESSAGES/exception.po
- [ ] gl.po  
https://github.com/IsaoCorp/goalous2/blob/topic-translate-english/app/Locale/eng/LC_MESSAGES/gl.po
- [ ] mail.po  
https://github.com/IsaoCorp/goalous2/blob/topic-translate-english/app/Locale/eng/LC_MESSAGES/mail.po
- [ ] notify.po  
https://github.com/IsaoCorp/goalous2/blob/topic-translate-english/app/Locale/eng/LC_MESSAGES/notify.po
- [ ] time.po  
https://github.com/IsaoCorp/goalous2/blob/topic-translate-english/app/Locale/eng/LC_MESSAGES/time.po
- [ ] validate.po  
https://github.com/IsaoCorp/goalous2/blob/topic-translate-english/app/Locale/eng/LC_MESSAGES/validate.po

## 翻訳の手順

1. 上記のリンクをクリック。
1. 編集ボタンをクリック。  
![goalous2_exception_po_at_topic-translate-english_ _isaocorp_goalous2](https://cloud.githubusercontent.com/assets/3040037/5136256/2306cf10-7168-11e4-80cb-9686fadab037.png)   
1. ファイルを編集。  
![editing_goalous2_exception_po_at_topic-translate-english_ _isaocorp_goalous2](https://cloud.githubusercontent.com/assets/3040037/5136289/d95a89b4-7168-11e4-8169-ba5218d3bc2d.png)
1. 変更内容を記載し、「commit changes」ボタンを押す。  
![editing_goalous2_exception_po_at_topic-translate-english_ _isaocorp_goalous2](https://cloud.githubusercontent.com/assets/3040037/5136273/7528bd44-7168-11e4-8541-9ff3a8141e9d.png)   
1. 以上

## 補足
- Commit Changesを押さないでページを閉じた場合、変更内容が破棄されるので気をつけてください。
- %sなどの記号はシステムで置き換えられる部分なので翻訳後の文章にも入れておいてください。
- 翻訳前の文章が記号のみなどは、なにも入れなくて大丈夫です。
