
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
1. 直接
