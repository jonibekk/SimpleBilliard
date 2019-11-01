<?php
App::uses('GoalousTestCase', 'Test');
App::import('Lib/Translation', 'GoogleTranslatorClient');

class GoogleTranslatorClientTest extends GoalousTestCase
{
    /**
     * For testing the Google Translation API directly.
     * Set to always be skipped anywhere except local
     */
    public function test_translateString_success()
    {
        $this->skipIf(ENV_NAME !== 'local');

        $translatorClient = new GoogleTranslatorClient();

        var_dump("The first ever book to be written.\nIt was not written on paper, nor on skin; it was on stone.");

        $result = $translatorClient->translate("The first ever book to be written.\nIt was not written on paper, nor on skin; it was on stone.",
            'ja');
        var_dump($result->getTranslation());

        $result = $translatorClient->translate("The first ever book to be written.<br>It was not written on paper, nor on skin; it was on stone.<br>",
            'ja');
        var_dump($result->getTranslation());

        $result = $translatorClient->translate("The first ever book to be written.<br translate='no'>It was not written on paper, nor on skin; it was on stone.<br translate='no'>",
            'ja');
        var_dump($result->getTranslation());

        $result = $translatorClient->translate("The first ever book to be written.<br class='glstrl' translate='no'>It was not written on paper, nor on skin; it was on stone.<br class='APy3hosHlTSK' translate='no'>",
            'ja');
        var_dump($result->getTranslation());
    }

}
