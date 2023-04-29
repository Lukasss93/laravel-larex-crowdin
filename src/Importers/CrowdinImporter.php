<?php

namespace Lukasss93\LarexCrowdin\Importers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\Larex\Contracts\Importer;
use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;

class CrowdinImporter implements Importer
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Import data from a Crowdin project to CSV';
    }

    /**
     * @inheritDoc
     */
    public function handle(LarexImportCommand $command): Collection
    {
        $include = Str::of($command->option('include'))->explode(',')->reject(fn ($i) => empty($i));
        $exclude = Str::of($command->option('exclude'))->explode(',')->reject(fn ($i) => empty($i));

        $command->info('');

        /** @var Crowdin $crowdin */
        $crowdin = app(Crowdin::class);

        //get project
        $command->warn('Getting project informations...');
        $project = $crowdin->project->get(config('larex-crowdin.project_id'));
        $projectID = $project->getId();
        $command->info("Project: [$projectID] {$project->getName()}");

        //get languages
        $sourceLanguage = $project->getSourceLanguageId();
        $targetLanguages = collect($project->getTargetLanguageIds())->sort()->values();
        $command->info("Source language: $sourceLanguage");
        $command->info('');

        //list source files
        $command->warn('Getting project source files list...');
        $files = $crowdin->fileList($projectID);
        $filesCount = count($files);
        $command->info("Project source files found: $filesCount");
        $command->info('');

        if ($filesCount === 0) {
            return collect();
        }

        //filter $targetLanguages by include/exclude option
        $targetLanguages = $targetLanguages
            ->when($include->isNotEmpty(), fn (Collection $c) => $c->intersect($include))
            ->when($exclude->isNotEmpty(), fn (Collection $c) => $c->diff($exclude));

        //download translation files per language
        $command->warn('Downloading project translation files...');
        $translations = [];
        $bar = $command->getOutput()->createProgressBar($targetLanguages->count());
        foreach ($targetLanguages as $languageID) {
            $export = $crowdin->translation->exportProjectTranslation($projectID, [
                'targetLanguageId' => $languageID,
                'format' => 'xliff',
                'skipUntranslatedStrings' => true,
            ]);
            $xliff = Http::get($export->getUrl())->body();
            $translations[$languageID] = $this->parseXliff($xliff);
            $bar->advance();
        }
        $bar->finish();
        $command->info('');
        $command->info('');

        //download source files
        $command->warn('Downloading project source files...');

        /** @var Collection<int,array<string,string>> $rows */
        $rows = collect();

        $bar = $command->getOutput()->createProgressBar(count($files));
        foreach ($files as $file) {
            $download = $crowdin->file->download($projectID, $file->getId());

            $group = pathinfo($file->getName(), PATHINFO_FILENAME);
            $content = collect(Arr::dot(Http::get($download->getUrl())->json()));

            $rows = $rows->merge($content->map(function ($item, $key) use (
                $group,
                $sourceLanguage,
                $targetLanguages,
                $translations
            ) {
                $out = [
                    'group' => $group,
                    'key' => $key,
                    $sourceLanguage => $item,
                ];

                foreach ($targetLanguages as $languageID) {
                    $out[$languageID] = $translations[$languageID][$group][$key] ?? null;
                }

                return $out;
            })->values());

            $bar->advance();
        }
        $bar->finish();
        $command->info('');
        $command->info('');

        return $rows;
    }

    protected function parseXliff(string $content): array
    {
        $output = [];

        $xml = simplexml_load_string($content);

        foreach ($xml->file as $group) {
            $group = (array)$group;
            $groupName = (string)Str::of($group['@attributes']['original'])
                ->substr(1)
                ->replace('.json', '');

            $body = (array)$group['body'];

            if (!array_key_exists('trans-unit', $body)) {
                continue;
            }

            if (!is_array($body['trans-unit'])) {
                $body['trans-unit'] = [$body['trans-unit']];
            }

            $output[$groupName] = collect($body['trans-unit'] ?? [])
                ->mapWithKeys(function ($item) {
                    $item = (array)$item;
                    $key = Str::start($item['@attributes']['resname'], '.');
                    $value = $item['target'];

                    return [substr($key, 1) => $value];
                })->toArray();
        }

        return $output;
    }
}
