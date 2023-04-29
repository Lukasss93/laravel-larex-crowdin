<?php

namespace Lukasss93\LarexCrowdin\Commands;

use CrowdinApiClient\Model\Language;
use Illuminate\Console\Command;
use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;

class LanguagesListCommand extends Command
{
    protected $signature = 'larex-crowdin:languages';

    protected $description = 'List Supported Languages';

    public function handle(Crowdin $crowdin): int
    {
        $supportedLanguages = collect($crowdin->languageList())
            ->map(fn (Language $item) => [$item->getId(), $item->getName()])
            ->sortBy(0)
            ->values()
            ->toArray();

        $this->table(['code', 'name'], $supportedLanguages);

        return 0;
    }
}
