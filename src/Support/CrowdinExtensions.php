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
}
