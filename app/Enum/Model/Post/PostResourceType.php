<?php

namespace Goalous\Enum\Model\Post;

use MyCLabs\Enum\Enum;

/**
 * @method static static NONE()
 * @method static static VIDEO_STREAM()
 * @method static static IMAGE()
 * @method static static FILE()
 */
class PostResourceType extends Enum
{
    /**
     * Uploaded image user can view
     * Refer attached_files table
     * AttachedFileType::TYPE_FILE_IMG = 0
     */
    const IMAGE = 0;

    /**
     * This is for video streaming on video player.
     * Refer video_streams table
     */
    const VIDEO_STREAM = 1;

    /**
     * Download file.
     * Refer attached_files table
     * AttachedFileType::TYPE_FILE_DOC = 2
     */
    const FILE = 2;

    /**
     * Video file (not transcoded)
     * Refer attached_files table
     * AttachedFileType::TYPE_FILE_VIDEO = 1
     */
    const FILE_VIDEO = 3;
}
