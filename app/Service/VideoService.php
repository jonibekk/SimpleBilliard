<?php
App::import('Service', 'AppService');
App::uses('VideoUploadRequestOnPost', 'Model/Video/Requests');
App::uses('VideoStorageClient', 'Model/Video');

App::uses('Video', 'Model');
App::uses('VideoStream', 'Model');

use Goalous\Enum as Enum;

/**
 * Class VideoService
 */
class VideoService extends AppService
{
}
