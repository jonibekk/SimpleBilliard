<?php
App::import('Service', 'AppService');
App::uses('UploadHelper', 'View/Helper');

/**
 * Deal image by integrating cloud storage
 * Upload image, Get image url, etc
 */
class ImageStorageService extends AppService
{
    /**
     * Get image urls(s3) each size
     * TODO: Stop using UploadHelper to get s3 url.
     * It is wrong that Helper is called from Service.
     * But currently, it is high risky to use new Class instead of UploadHelper Because it depend on UploadBehavior further more.
     * Related AppModel.attachImgUrl
     *
     * @param array  $data
     * @param string $modelName
     * @param string $photoKey
     * @param array  $photoStyles
     *
     * @return array
     */
    public function getImgUrlEachSize(array $data, string $modelName, string $photoKey = 'photo', array $photoStyles = []) :array
    {
        try {
            App::uses($modelName, 'Model');
            $Model = ClassRegistry::init($modelName);
        } catch (\Throwable $e) {
            GoalousLog::emergency('', [
                'message'  => $e->getMessage(),
                'modelName' => $modelName,
            ]);
            GoalousLog::emergency($e->getTraceAsString());

            return array_fill_keys($photoStyles, '');
        }

        $upload = new UploadHelper(new View());
        $defaultStyles = array_keys($Model->actsAs['Upload'][$photoKey]['styles']);
        if (empty($photoStyles)) {
            $photoStyles = $defaultStyles;
            $photoStyles[] = 'original';
        }

        $imgUrls = [];
        foreach ($photoStyles as $style) {
            if ($style != 'original' && !in_array($style, $defaultStyles)) {
                continue;
            }
            $imgUrls[$style] = $upload->uploadUrl($data,
                "$modelName.photo",
                ['style' => $style],
                true);
        }
        return $imgUrls;
    }
}
