<?php

namespace Lukasss93\LarexCrowdin\Support\Crowdin;

use CrowdinApiClient\Api\ApiInterface;
use Illuminate\Support\Str;

/**
 * Class Crowdin.
 *
 * @property \Lukasss93\LarexCrowdin\Support\Crowdin\StorageApi storage
 * @inheritdoc
 */
class Crowdin extends \CrowdinApiClient\Crowdin
{
    protected array $overriddenClasses = [
        \CrowdinApiClient\Api\StorageApi::class => \Lukasss93\LarexCrowdin\Support\Crowdin\StorageApi::class,
    ];

    /**
     * @param string $name
     * @return ApiInterface
     */
    public function getApi(string $name): ApiInterface
    {
        $class = '\CrowdinApiClient\\Api\\'.ucfirst($name).'Api';

        if ($this->isEnterprise) {
            $_class = '\CrowdinApiClient\\Api\\Enterprise\\'.ucfirst($name).'Api';

            if (class_exists($_class)) {
                $class = $_class;
            }
        }

        if (!array_key_exists($class, $this->apis)) {
            $override = collect($this->overriddenClasses)
                ->first(fn ($item, $key) => Str::replaceFirst('\\', '', $class) === $key);

            if ($override !== null) {
                $class = $override;
            }

            $this->apis[$class] = new $class($this);
        }

        return $this->apis[$class];
    }

    public function fileList(int $projectID, int $limit = 500): array
    {
        $files = [];
        $offset = 0;
        do {
            $currentFiles = $this->file->list($projectID, ['offset' => $offset, 'limit' => $limit]);
            $end = count($currentFiles) === 0;
            $offset += $limit;
            $files = array_merge($files, iterator_to_array($currentFiles->getIterator()));
        } while (!$end);

        return $files;
    }

    public function languageList(int $limit = 500): array
    {
        $items = [];
        $offset = 0;
        do {
            $currentFiles = $this->language->list(['offset' => $offset, 'limit' => $limit]);
            $end = count($currentFiles) === 0;
            $offset += $limit;
            $items = array_merge($items, iterator_to_array($currentFiles->getIterator()));
        } while (!$end);

        return $items;
    }
}
