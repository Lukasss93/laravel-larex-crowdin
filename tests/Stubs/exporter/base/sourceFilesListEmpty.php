<?php

use CrowdinApiClient\ModelCollection;

$data = new ModelCollection();
$data->setPagination([
    'offset' => 0,
    'limit' => 500,
]);

return $data;
