<?php

App::uses('GoalousTestCase', 'Test');
App::uses('UrlUtil', 'Util');


class UrlUtilTest extends GoalousTestCase
{
    public function test_encapsulateUrl_success()
    {
        $sampleText1 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
        $sampleText2 = "https://www.google.com Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
        $sampleText3 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. https://www.google.com";

        $processedSampleText1 = UrlUtil::encapsulateUrl($sampleText1, ["http", "https"], "12", "34");
        $processedSampleText2 = UrlUtil::encapsulateUrl($sampleText2, ["http", "https"], "12", "34");
        $processedSampleText3 = UrlUtil::encapsulateUrl($sampleText3, ["http", "https"], "12", "34");

        $this->assertTextEquals($sampleText1, $processedSampleText1);
        $this->assertTextEquals(
            "12https://www.google.com34 Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
            $processedSampleText2
        );
        $this->assertTextEquals(
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. 12https://www.google.com34",
            $processedSampleText3
        );

        $sampleTextUTF1 = "无人爱苦，亦无人寻之欲之，乃因其苦";
        $sampleTextUTF2 = "https://www.google.com 无人爱苦，亦无人寻之欲之，乃因其苦";
        $sampleTextUTF3 = "无人爱苦，亦无人寻之欲之，乃因其苦 https://www.google.com";

        $processedSampleTextUTF1 = UrlUtil::encapsulateUrl($sampleTextUTF1, ["http", "https"], "12", "34");
        $processedSampleTextUTF2 = UrlUtil::encapsulateUrl($sampleTextUTF2, ["http", "https"], "12", "34");
        $processedSampleTextUTF3 = UrlUtil::encapsulateUrl($sampleTextUTF3, ["http", "https"], "12", "34");

        $this->assertTextEquals($sampleTextUTF1, $processedSampleTextUTF1);
        $this->assertTextEquals("12https://www.google.com34 无人爱苦，亦无人寻之欲之，乃因其苦", $processedSampleTextUTF2);
        $this->assertTextEquals("无人爱苦，亦无人寻之欲之，乃因其苦 12https://www.google.com34", $processedSampleTextUTF3);

        $sampleTextUTF1 = "无人爱苦，亦无人寻之欲之，乃因其苦";
        $sampleTextUTF2 = "https://www.google.com 无人爱苦，亦无人寻之欲之，乃因其苦 ftp://somefile.com/file1";
        $sampleTextUTF3 = "ftp://somefile.com/file1 无人爱苦，亦无人寻之欲之，乃因其苦 https://www.google.com";

        $processedSampleTextUTF1 = UrlUtil::encapsulateUrl($sampleTextUTF1, ["ftp"], "12", "34");
        $processedSampleTextUTF2 = UrlUtil::encapsulateUrl($sampleTextUTF2, ["ftp"], "12", "34");
        $processedSampleTextUTF3 = UrlUtil::encapsulateUrl($sampleTextUTF3, ["ftp"], "12", "34");

        $this->assertTextEquals($sampleTextUTF1, $processedSampleTextUTF1);
        $this->assertTextEquals(
            "https://www.google.com 无人爱苦，亦无人寻之欲之，乃因其苦 12ftp://somefile.com/file134",
            $processedSampleTextUTF2
        );
        $this->assertTextEquals(
            "12ftp://somefile.com/file134 无人爱苦，亦无人寻之欲之，乃因其苦 https://www.google.com",
            $processedSampleTextUTF3
        );
    }

    /**
 * Function to test encapsulating Goalous (CakePHP 2.0) styled URLs
 */
    public function test_encapsulateGoalousURL_success()
    {
        $sampleText = "
  https://isao.goalous.com/goals/view_actions/goal_id:2682/page_type:list/key_result_id:6971
  https://isao.goalous.com/goals/view_actions/goal_id:2615/page_type:list/key_result_id:6837
";

        $expectedText = "
  12https://isao.goalous.com/goals/view_actions/goal_id:2682/page_type:list/key_result_id:697134
  12https://isao.goalous.com/goals/view_actions/goal_id:2615/page_type:list/key_result_id:683734
";

        $processedSample = UrlUtil::encapsulateUrl($sampleText, ["http", "https"], "12", "34");

        $this->assertTextEquals($expectedText, $processedSample);
    }

    /**
     * Function to test encapsulating Goalous (CakePHP 2.0) styled URLs
     */
    public function test_encapsulateMultipleURL_success()
    {
        $sampleText = "[サークルとアクションのテンプレート]
  GitHubの問題テンプレートは私のイメージです。
  https://docs.github.com/en/free-pro-team@latest/github/building-a-strong-community/configuring-issue-templates-for-your-repository

 一部のメンバーは、同じKRのアクションで同じ形式を使用します。
  https://isao.goalous.com/goals/view_actions/goal_id:2682/page_type:list/key_result_id:6971
  https://isao.goalous.com/goals/view_actions/goal_id:2615/page_type:list/key_result_id:6837
  （花田さん）

 この機能により、コンテンツの投稿と思考が簡単になります。

 そして、それは外部サービス（RPA、DB、SaaS ....）との統合に関連します。";

        $expectedText = "[サークルとアクションのテンプレート]
  GitHubの問題テンプレートは私のイメージです。
  12https://docs.github.com/en/free-pro-team@latest/github/building-a-strong-community/configuring-issue-templates-for-your-repository34

 一部のメンバーは、同じKRのアクションで同じ形式を使用します。
  12https://isao.goalous.com/goals/view_actions/goal_id:2682/page_type:list/key_result_id:697134
  12https://isao.goalous.com/goals/view_actions/goal_id:2615/page_type:list/key_result_id:683734
  （花田さん）

 この機能により、コンテンツの投稿と思考が簡単になります。

 そして、それは外部サービス（RPA、DB、SaaS ....）との統合に関連します。";

        $processedSample = UrlUtil::encapsulateUrl($sampleText, ["http", "https"], "12", "34");

        $this->assertTextEquals($expectedText, $processedSample);
    }

}
