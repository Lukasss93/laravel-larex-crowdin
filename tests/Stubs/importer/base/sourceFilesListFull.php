<?php

use CrowdinApiClient\Model\File as CrowdinFile;
use CrowdinApiClient\ModelCollection;

$data = new ModelCollection();
$data->setPagination([
    'offset' => 0,
    'limit' => 500,
]);

$files = [
    new CrowdinFile([
        'id' => 37,
        'projectId' => 446960,
        'branchId' => 0,
        'directoryId' => 0,
        'name' => 'app.json',
        'title' => '',
        'type' => 'json',
        'path' => '/app.json',
        'status' => 'active',
        'revisionId' => 3,
        'priority' => 'normal',
        'excludedTargetLanguages' => null,
        'createdAt' => '2021-07-31T22:43:19+00:00',
        'updatedAt' => '2021-08-01T16:48:23+00:00',
    ]),
    new CrowdinFile([
        'id' => 39,
        'projectId' => 446960,
        'branchId' => 0,
        'directoryId' => 0,
        'name' => 'color.json',
        'title' => '',
        'type' => 'json',
        'path' => '/color.json',
        'status' => 'active',
        'revisionId' => 1,
        'priority' => 'normal',
        'excludedTargetLanguages' => null,
        'createdAt' => '2021-08-01T16:48:26+00:00',
        'updatedAt' => '2021-08-01T16:48:26+00:00',
    ]),
    new CrowdinFile([
        'id' => 41,
        'projectId' => 446960,
        'branchId' => 0,
        'directoryId' => 0,
        'name' => 'device.json',
        'title' => '',
        'type' => 'json',
        'path' => '/device.json',
        'status' => 'active',
        'revisionId' => 1,
        'priority' => 'normal',
        'excludedTargetLanguages' => null,
        'createdAt' => '2021-08-01T16:48:30+00:00',
        'updatedAt' => '2021-08-01T16:48:30+00:00',
    ]),
    new CrowdinFile([
        'id' => 43,
        'projectId' => 446960,
        'branchId' => 0,
        'directoryId' => 0,
        'name' => 'number.json',
        'title' => '',
        'type' => 'json',
        'path' => '/number.json',
        'status' => 'active',
        'revisionId' => 1,
        'priority' => 'normal',
        'excludedTargetLanguages' => null,
        'createdAt' => '2021-08-01T17:49:55+00:00',
        'updatedAt' => '2021-08-01T17:49:56+00:00',
    ]),
];

foreach ($files as $file) {
    $data->add($file);
}

return $data;
