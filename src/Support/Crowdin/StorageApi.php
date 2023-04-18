<?php

namespace Lukasss93\LarexCrowdin\Support\Crowdin;

use CrowdinApiClient\Http\ResponseDecorator\ResponseModelDecorator;
use CrowdinApiClient\Model\Storage;
use InvalidArgumentException;
use SplFileObject;

class StorageApi extends \CrowdinApiClient\Api\StorageApi
{
    /**
     * Add Storage.
     *
     * @link https://support.crowdin.com/api/v2/#operation/api.storages.post API Documentation
     * @link https://support.crowdin.com/enterprise/api/#operation/api.storages.post API Documentation Enterprise
     *
     * @param SplFileObject|VirtualSplFileObject $fileInfo
     * @return Storage|null
     */
    public function create($fileInfo): ?Storage
    {
        if ($fileInfo instanceof SplFileObject) {
            $name = $fileInfo->getFilename();
            $content = file_get_contents($fileInfo->getRealPath());
        } elseif ($fileInfo instanceof VirtualSplFileObject) {
            $name = $fileInfo->getName();
            $content = $fileInfo->getContent();
        } else {
            throw new InvalidArgumentException('$fileInfo field must be SplFileObject or VirtualSplFileObject.');
        }

        $options = [
            'headers' => [
                'Crowdin-API-FileName' => $name,
            ],
            'body' => $content,
        ];

        return $this->client->apiRequest('post', 'storages', new ResponseModelDecorator(Storage::class), $options);
    }
}
