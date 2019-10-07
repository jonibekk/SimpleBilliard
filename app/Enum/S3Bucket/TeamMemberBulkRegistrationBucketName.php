<?php

namespace Goalous\Enum\S3Bucket;

use MyCLabs\Enum\Enum;

/**
 * Class TeamMemberBulkRegistrationBucketName
 * @package Goalous\Enum
 */
class TeamMemberBulkRegistrationBucketName extends Enum
{
    const DEV = 'goalous-test-csv-bulk-registration';
    const ISAO = 'goalous-isao-csv-bulk-registration';
    const WWW = 'goalous-www-csv-bulk-registration';
}
