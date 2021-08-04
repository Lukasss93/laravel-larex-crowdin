<?php

namespace Lukasss93\LarexCrowdin\Support\Crowdin;

use CrowdinApiClient\Api\ApiInterface;
use Illuminate\Support\Str;

/**
 * Class Crowdin
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
}
