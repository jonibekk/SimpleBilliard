<?php

class StringUtil
{
    /**
     * Get string length using UTF-8 encoding.
     *
     * @param string $input
     *
     * @return int
     */
    public static function mbStrLength(string $input): int
    {
        return mb_strlen($input, 'UTF-8');
    }

    /**
     * Split long string multiple segments, with each segment having maximum of specified char length
     *
     * @param string $input                Base string
     * @param int    $maxCharLength        Maximum char length per segment.
     * @param string $delimiter            String used to split the string. By default, split by new line
     * @param string $delimiterReplacement String used to replace previous delimiter. By default keep using new line
     * @param bool   $forceSplit           Force splitting even though string is shorter than max char length
     *
     * @return array
     */
    public static function splitStringToSegments(
        string $input,
        int $maxCharLength = 2000,
        string $delimiter = "\n",
        string $delimiterReplacement = "\n",
        bool $forceSplit = false
    ): array {
        $segmentedString = [];

        if (empty($input)) {
            return $segmentedString;
        }

        $originalLocale = setlocale(LC_CTYPE, 0);
        setlocale(LC_CTYPE, 'C.UTF-8');

        if (!$forceSplit && self::mbStrLength($input) < $maxCharLength) {
            return [$input];
        }

        $splitInput = mb_split($delimiter, $input);

        for ($splitInputIndex = 0, $segmentedStringIndex = 0; $splitInputIndex < count($splitInput);) {

            if (empty($segmentedString[$segmentedStringIndex])) {
                $segmentedString[$segmentedStringIndex] = $splitInput[$splitInputIndex];
                $splitInputIndex++;
                continue;
            }

            if (self::mbStrLength($segmentedString[$segmentedStringIndex]) + self::mbStrLength($splitInput[$splitInputIndex]) + self::mbStrLength($delimiterReplacement) <= $maxCharLength) {
                $segmentedString[$segmentedStringIndex] .= $delimiterReplacement . $splitInput[$splitInputIndex];
                $splitInputIndex++;
            } else {
                $segmentedStringIndex++;
            }
        }

        setlocale(LC_CTYPE, $originalLocale);

        return $segmentedString;
    }

    /**
     * Merge segmented strings array into a single string.
     *
     * @param array  $input
     * @param string $glue
     *
     * @return string
     */
    public static function mergeSegmentsToString(array $input, string $glue = "\n"): string
    {
        $mergedString = '';

        if (empty($input)) {
            return $mergedString;
        }

        foreach ($input as $string) {
            if (empty($mergedString)) {
                $mergedString = $string;
                continue;
            }
            $mergedString .= $glue . $string;
        }

        return $mergedString;
    }
}
