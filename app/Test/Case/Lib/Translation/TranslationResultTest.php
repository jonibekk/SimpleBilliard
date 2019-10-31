<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Translation', 'TranslationResult');


class TranslationResultTest extends GoalousTestCase
{
    public function test_getCharLength_success()
    {
        $translation = new TranslationResult('ja', 'Some translation here', 'en');

        $this->assertEquals(21, $translation->getTranslationCharLength());

        $translation = new TranslationResult('en', "翻訳できる文字数の上限に近づいています。", 'ja');

        $this->assertEquals(20, $translation->getTranslationCharLength());
    }

    public function test_toArray_success()
    {
        $translation = new TranslationResult('ja', 'Some translation here', 'en');

        $expectedArray = [
            'source_language' => 'ja',
            'language'        => 'en',
            'translation'     => 'Some translation here'
        ];

        $this->assertEquals($expectedArray, $translation->toArray());
    }
}
