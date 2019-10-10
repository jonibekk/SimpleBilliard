<?php
App::uses('GoalousTestCase', 'Test');
App::uses('TeamMemberBulkRegisterValidator', 'Validator/Csv');

class TeamMemberBulkRegisterValidatorTest extends GoalousTestCase
{
    /**
     * @return array
     */
    public function validateNormalDataProvider(): array
    {
        return [
            'normal::admin_flg:on' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'normal::admin_flg:off' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'off',
                    'language' => 'eng',
                ]
            ],
            'normal::language:jpn' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'off',
                    'language' => 'jpn',
                ]
            ],
            'normal::language:eng' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'eng',
                ]
            ],
        ];
    }

    /**
     * @param array $record
     * @dataProvider validateNormalDataProvider
     * @throws Exception
     */
    public function testValidateNormal(array $record)
    {
        $this->assertTrue(TeamMemberBulkRegisterValidator::createDefaultValidator()->validate($record));
    }
    /**
     * @return array
     */
    public function validateAbnormalDataProvider(): array
    {
        return [
            'abnormal::email format is incorrect' => [
                'record' => [
                    'email' => 'hoge.yamada',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::email exceeds the character limit.' => [
                'record' => [
                    'email' => str_repeat( 'a', TeamMemberBulkRegisterValidator::EMAIL_MAX) . '@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::email is empty' => [
                'record' => [
                    'email' => '',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::first_name is not a string type' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 1234567890,
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::first_name does not match username regular expression' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => '!"#$%&',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::first_name exceeds the character limit.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => str_repeat( 'A', TeamMemberBulkRegisterValidator::FIRST_NAME_MAX + 1),
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::first_name is empty.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => '',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::last_name is not a string type' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 1234567890,
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::last_name does not match username regular expression' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => '!"#$%&',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::last_name exceeds the character limit.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => str_repeat( 'A', TeamMemberBulkRegisterValidator::LAST_NAME_MAX + 1),
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::last_name is empty.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => '',
                    'admin_flg' => 'on',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::admin flg is not a string type' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 1234,
                    'language' => 'jpn',
                ]
            ],
            'abnormal::admin_flg value is invalid.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'yes',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::admin_flg is empty.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => '',
                    'language' => 'jpn',
                ]
            ],
            'abnormal::language is not a string_type' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 1234,
                    'language' => 'jpn',
                ]
            ],
            'abnormal::language value is invalid.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => 'fra',
                ]
            ],
            'abnormal::language is empty.' => [
                'record' => [
                    'email' => 'hoge.yamada@isao.co.jp',
                    'first_name' => 'Hoge',
                    'last_name' => 'Yamada',
                    'admin_flg' => 'on',
                    'language' => '',
                ]
            ],
        ];
    }

    /**
     * @param array $record
     * @dataProvider validateAbnormalDataProvider
     * @expectedException \Respect\Validation\Exceptions\AllOfException
     * @throws Exception
     */
    public function testValidateAbNormal(array $record)
    {
        TeamMemberBulkRegisterValidator::createDefaultValidator()->validate($record);
    }
}
