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
     * @param SplFileObject|VirtualSplFileObject $fileObject
     * @return Storage|null
     */
    public function create($fileObject): ?Storage
    {
        if ($fileObject instanceof SplFileObject) {
            $name = $fileObject->getFilename();
            $content = file_get_contents($fileObject->getRealPath());
        } elseif ($fileObject instanceof VirtualSplFileObject) {
            $name = $fileObject->getName();
            $content = $fileObject->getContent();
        } else {
            throw new InvalidArgumentException('$fileObject field must be SplFileObject or VirtualSplFileObject.');
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
