<?php

use CrowdinApiClient\Model\Project;

return new Project([
    'id' => 123456,
    'groupId' => 0,
    'userId' => 12345678,
    'sourceLanguageId' => 'en',
    'targetLanguageIds' => ['es-ES'],
    'languageAccessPolicy' => 'open',
    'name' => 'Foo Project',
    'identifier' => 'foo-project',
    'description' => 'Foo Description',
    'visibility' => 'open',
]);
