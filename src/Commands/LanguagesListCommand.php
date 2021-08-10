<?php

namespace Lukasss93\LarexCrowdin\Commands;

use CrowdinApiClient\Model\Language;
use Illuminate\Console\Command;
use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;
use Lukasss93\LarexCrowdin\Support\CrowdinExtensions;

class LanguagesListCommand extends Command
{
    protected $signature = 'larex-crowdin:languages';

    protected $description = 'List Supported Languages';

    public function handle(): int
    {
        /** @var Crowdin $crowdin */
        $crowdin = app(Crowdin::class);

        $supportedLanguages = collect(CrowdinExtensions::languageList($crowdin))
            ->map(fn (Language $item) => [$item->getId(), $item->getName()])
            ->sortBy(0)
            ->values()
            ->toArray();

        $this->table(['code', 'name'], $supportedLanguages);

        return 0;
    }
}
