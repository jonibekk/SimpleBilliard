<?php
App::import('Service', 'CircleService');
App::import('Lib/DataExtender/Extension', 'DataExtension');

class CircleMemberCountExtension extends DataExtension
{
    protected function fetchData(array $keys): array
    {
        $circleIds = $this->filterKeys($keys);

        /** @var CircleService $CircleService */
        $CircleService = ClassRegistry::init('CircleService');

        $res = $CircleService->getMemberCountEachCircle($circleIds);
        return $res;
    }

    protected function connectData(
        array $parentData,
        string $parentKeyName,
        array $extData,
        string $extDataKey
    ): array {
        foreach ($parentData as $key => &$parentElement) {
            if (!is_int($key)){
                $extKey = Hash::get($parentData, $parentKeyName);
                $parentData['circle_member_count'] = isset($extData[$extKey]) ? $extData[$extKey] : 0;
                return $parentData;
            }
            $extKey = Hash::get($parentElement, $parentKeyName);
            $parentElement['circle_member_count'] = isset($extData[$extKey]) ? $extData[$extKey] : 0;
        }
        return $parentData;
    }

}
