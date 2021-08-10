<?php

namespace Lukasss93\LarexCrowdin\Support;

use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;

class CrowdinExtensions
{
    public static function fileList(Crowdin $crowdin, string $projectID, int $limit = 500): array
    {
        $files = [];
        $end = false;
        $offset = 0;
        do {
            $currentFiles = $crowdin->file->list($projectID, ['offset' => $offset, 'limit' => $limit]);
            $end = count($currentFiles) === 0;
            $offset += $limit;
            $files = array_merge($files, iterator_to_array($currentFiles->getIterator()));
        } while (!$end);

        return $files;
    }

    public static function languageList(Crowdin $crowdin, int $limit = 500): array
    {
        $items = [];
        $end = false;
        $offset = 0;
        do {
            $currentFiles = $crowdin->language->list(['offset' => $offset, 'limit' => $limit]);
            $end = count($currentFiles) === 0;
            $offset += $limit;
            $items = array_merge($items, iterator_to_array($currentFiles->getIterator()));
        } while (!$end);

        return $items;
    }
}
