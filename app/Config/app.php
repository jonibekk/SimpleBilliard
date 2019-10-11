<?php

$config['label'] = [
    'units' => [
        ['id' => 0, 'unit' => '%', 'label' => __('Percentage')],
        ['id' => 3, 'unit' => '¥', 'label' => __('Yen')],
        ['id' => 4, 'unit' => '$', 'label' => __('Dollar')],
        ['id' => 1, 'unit' => '#', 'label' => __('Other numeric')],
        ['id' => 2, 'unit' => '-', 'label' => __('Complete/Incomplete')],
    ],
    'priorities' => [
        ['id' => 1, 'label' => __('1 (Very low)')],
        ['id' => 2, 'label' => __('2')],
        ['id' => 3, 'label' => __('3 (Default)')],
        ['id' => 4, 'label' => __('4')],
        ['id' => 5, 'label' => __('5 (Very high)')],
    ],
];
$config['allow_image_types'] = [
    'image/png',
    'image/gif',
    'image/jpeg',
];

/**
 * @see Video Media mime-types listed in IANA, and changed to lower case
 *      https://www.iana.org/assignments/media-types/media-types.xhtml#video
 *
 * or we can choose apache defined mime-types for less but general choice
 *      http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
 */
$config['allow_video_types'] = [
    // this video mime-types is referred from iana.org list
    'video/1d-interleaved-parityfec',
    'video/3gpp',
    'video/3gpp2',
    'video/3gpp-tt',
    'video/bmpeg',
    'video/bt656',
    'video/celb',
    'video/dv',
    'video/encaprtp',
    'video/example',
    'video/h261',
    'video/h263',
    'video/h263-1998',
    'video/h263-2000',
    'video/h264',
    'video/h264-rcdo',
    'video/h264-svc',
    'video/h265',
    'video/iso.segment',
    'video/jpeg',
    'video/jpeg2000',
    'video/mj2',
    'video/mp1s',
    'video/mp2p',
    'video/mp2t',
    'video/mp4',
    'video/mp4v-es',
    'video/mpv',
    'video/mpeg4-generic',
    'video/nv',
    'video/ogg',
    'video/pointer',
    'video/quicktime',
    'video/raptorfec',
    'video/rtp-enc-aescm128',
    'video/rtploopback',
    'video/rtx',
    'video/smpte291',
    'video/smpte292m',
    'video/ulpfec',
    'video/vc1',
    'video/vnd.cctv',
    'video/vnd.dece.hd',
    'video/vnd.dece.mobile',
    'video/vnd.dece-mp4',
    'video/vnd.dece.pd',
    'video/vnd.dece.sd',
    'video/vnd.dece.video',
    'video/vnd.directv-mpeg',
    'video/vnd.directv.mpeg-tts',
    'video/vnd.dlna.mpeg-tts',
    'video/vnd.dvb.file',
    'video/vnd.fvt',
    'video/vnd.hns.video',
    'video/vnd.iptvforum.1dparityfec-1010',
    'video/vnd.iptvforum.1dparityfec-2005',
    'video/vnd.iptvforum.2dparityfec-1010',
    'video/vnd.iptvforum.2dparityfec-2005',
    'video/vnd.iptvforum.ttsavc',
    'video/vnd.iptvforum.ttsmpeg2',
    'video/vnd.motorola.video',
    'video/vnd.motorola.videop',
    'video/vnd-mpegurl',
    'video/vnd.ms-playready.media.pyv',
    'video/vnd.nokia.interleaved-multimedia',
    'video/vnd.nokia.mp4vr',
    'video/vnd.nokia.videovoip',
    'video/vnd.objectvideo',
    'video/vnd.radgamettools.bink',
    'video/vnd.radgamettools.smacker',
    'video/vnd.sealed.mpeg1',
    'video/vnd.sealed.mpeg4',
    'video/vnd.sealed-swf',
    'video/vnd.sealedmedia.softseal-mov',
    'video/vnd.uvvu-mp4',
    'video/vnd-vivo',
    'video/vp8',
    'video/webm',

    // Windows Media Files
    // @see https://msdn.microsoft.com/ja-jp/library/cc430207.aspx
    // .wmv
    'video/x-ms-asf',
    'video/x-ms-wmv',
    // .avi
    'video/x-msvideo',

    // .flv
    'video/x-flv',
    // .mkv
    'video/x-matroska',
];

/**
 * Be sure to keep same list in front-end
 * https://github.com/IsaoCorp/goalous-front-end/blob/develop/src/app/core/services/file.service.ts
 */
$config['image_file_types'] = [
    'gif',
    'ief',
    'jpe',
    'jpeg',
    'jpg',
    'pbm',
    'pgm',
    'png',
    'pnm',
    'ppm',
    'ras',
    'rgb',
    'tif',
    'tiff',
    'xbm',
    'xpm',
    'xwd',
    'svg',
    'svgz'
];

/**
 * Be sure to keep same list in front-end
 * https://github.com/IsaoCorp/goalous-front-end/blob/develop/src/app/core/services/file.service.ts
 */
$config['video_file_types'] = [
    'mp4',
    'webm',
    'mpeg4',
    '3gpp',
    'hevc',
    'mov',
    'avi',
    'mpegps',
    'wmv',
    'flv',
    'ogg',
    'mpeg',
    '3gp',
    'mpg',
    'ogm',
    'mkv',
    'ogv'
];

