<?php
App::uses('CakeTestFixtureEx', 'Test/Fixture');

/**
 * Fixture for mst_translation_languages
 *
 * Class MstTranslationLanguageFixture
 */
class MstTranslationLanguageFixture extends CakeTestFixtureEx
{
    public $fields = [
        'id'              => ['type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'],
        'language'        => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 10, 'key' => 'unique', 'collate' => 'utf8mb4_general_ci', 'comment' => 'ISO 639-1 Language code', 'charset' => 'utf8mb4'],
        'importance'      => ['type' => 'integer', 'null' => false, 'default' => '0', 'length' => 4, 'unsigned' => false, 'comment' => 'Language importance'],
        'intl_name'       => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8mb4_general_ci', 'comment' => 'International name of the language. e.g. Japanese', 'charset' => 'utf8mb4'],
        'local_name'      => ['type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'collate' => 'utf8mb4_general_ci', 'comment' => 'Local name of the language. e.g. 日本語', 'charset' => 'utf8mb4'],
        'del_flg'         => ['type' => 'boolean', 'null' => false, 'default' => '0'],
        'deleted'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'created'         => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'modified'        => ['type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true],
        'indexes'         => [
            'PRIMARY'  => ['column' => 'id', 'unique' => 1],
            'language' => ['column' => 'language', 'unique' => 1]
        ],
        'tableParameters' => ['charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB']
    ];

    public $records = [
        ['id' => 1, 'language' => 'en', 'importance' => 255, 'intl_name' => 'English', 'local_name' => 'English'],
        ['id' => 2, 'language' => 'ja', 'importance' => 100, 'intl_name' => 'Japanese', 'local_name' => '日本語'],
        ['id' => 3, 'language' => 'zh-CN', 'importance' => 90, 'intl_name' => 'Chinese(Simplified)', 'local_name' => '简体中文'],
        ['id' => 4, 'language' => 'zh-TW', 'importance' => 50, 'intl_name' => 'Chinese(Traditional)', 'local_name' => '繁體中文'],
        ['id' => 5, 'language' => 'es', 'importance' => 80, 'intl_name' => 'Spanish', 'local_name' => 'Español'],
        ['id' => 6, 'language' => 'de', 'importance' => 60, 'intl_name' => 'German', 'local_name' => 'Deutsch'],
        ['id' => 7, 'language' => 'fr', 'importance' => 50, 'intl_name' => 'French', 'local_name' => 'Français'],
        ['id' => 8, 'language' => 'it', 'importance' => 40, 'intl_name' => 'Italian', 'local_name' => 'Italiano'],
        ['id' => 9, 'language' => 'id', 'importance' => 30, 'intl_name' => 'Indonesian', 'local_name' => 'Bahasa Indonesia'],
        ['id' => 10, 'language' => 'ms', 'importance' => 30, 'intl_name' => 'Malay', 'local_name' => 'Bahasa Melayu'],
        ['id' => 11, 'language' => 'th', 'importance' => 20, 'intl_name' => 'Thai', 'local_name' => 'ไทย']
    ];
}