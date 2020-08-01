<?php

class SearchApiService
{
    /**
     * @param $searchModel
     *
     * @return array
     */
    public function modelToArray($searchModel): array
    {
        $array = [];

        foreach ($searchModel as $key => $value) {
            if (is_object($value)) {
                $array[$key] = $this->modelToArray($value);
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}
