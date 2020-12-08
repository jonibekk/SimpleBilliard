<?php

class ConfigKeyResult
{
    static function getUnits(): array
    {
        $units = Configure::read('label.units');
        foreach ($units as $key => $unit) {
            $units[$key]['label'] = __($unit['label']);
        }
        return $units;
    }

    static function getPriorities(): array
    {
        $priorities = Configure::read('label.priorities');
        foreach ($priorities as $key => $priority) {
            $priorities[$key]['label'] = __($priority['label']);
        }
        return $priorities;
    }
}
