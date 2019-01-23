<?php
App::uses('GoalousTestCase', 'Test');

use Goalous\Enum as Enum;

class EnumTest extends GoalousTestCase
{
    function test_autoload()
    {
        // Autoload from relative path test
        $this->assertSame(2, Enum\ApiVersion\ApiVersion::VER_2);
        $this->assertSame(2, Enum\ApiVersion\ApiVersion::VER_2()->getValue());

        // Autoload from absolute path test
        $this->assertSame('validation', Goalous\Enum\Network\Response\ErrorType::VALIDATION);

        // below is a also test
        // will throw error if could not autoload
        Enum\TeamPlan::PAID;
        Enum\TranscodePattern::FULL;

        Enum\ApiVersion\ApiVersion::VER_2;
        Enum\AtobaraiCom\Credit::IN_JUDGE;

        Enum\DataType\DataType::INT;
        Enum\Stripe\StripeStatus::SUCCEEDED;

        Enum\Model\ChargeHistory\ChargeType::RECHARGE;
        Enum\Model\ChargeHistory\ResultType::ERROR;

        Enum\Model\Devices\DeviceType::ANDROID;

        Enum\Model\Evaluation\Status::DONE;
        Enum\Model\Invoice\CreditStatus::CANCELED;

        Enum\Model\PaymentSetting\Currency::JPY;
        Enum\Model\PaymentSetting\Type::CREDIT_CARD;
        Enum\Model\Post\PostResourceType::IMAGE;
        Enum\Model\Team\ServiceUseStatus::PAID;

        Enum\Model\TeamMember\Status::ACTIVE;

        Enum\Model\Term\EvaluateStatus::FINISHED;
        Enum\Model\Video\TranscodeOutputVersion::V1;
        Enum\Model\Video\Transcoder::AWS_ETS;
        Enum\Model\Video\VideoSourceType::NOT_RECOMMENDED;
        Enum\Model\Video\VideoTranscodeLogType::ERROR;
        Enum\Model\Video\VideoTranscodeProgress::COMPLETE;
        Enum\Model\Video\VideoTranscodeStatus::TRANSCODE_COMPLETE;

    }
}