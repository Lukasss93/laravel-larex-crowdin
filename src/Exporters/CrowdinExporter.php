<?php

namespace Lukasss93\LarexCrowdin\Exporters;

use CrowdinApiClient\Model\File;
use Fuse\Fuse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JsonException;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\Larex\Contracts\Exporter;
use Lukasss93\Larex\Support\CsvParser;
use Lukasss93\Larex\Support\CsvReader;
use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;
use Lukasss93\LarexCrowdin\Support\Crowdin\VirtualSplFileObject;
use SplFileObject;

class CrowdinExporter implements Exporter
{
    /**
     * @inheritDoc
     */
    public static function description(): string
    {
        return 'Export data from CSV to a Crowdin project';
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function handle(LarexExportCommand $command, CsvReader $reader): int
    {
        $include = Str::of($command->option('include'))->explode(',')->reject(fn ($i) => empty($i));
        $exclude = Str::of($command->option('exclude'))->explode(',')->reject(fn ($i) => empty($i));

        /** @var Crowdin $crowdin */
        $crowdin = app(Crowdin::class);

        //get project
        $command->warn('Getting project informations...');
        $project = $crowdin->project->get(config('larex-crowdin.project_id'));
        $projectID = $project->getId();
        $command->info("Project: [$projectID] {$project->getName()}");

        //get languages
        $sourceLanguage = $project->getSourceLanguageId();
        $command->info("Source language: $sourceLanguage");
        $command->info('');

        //parse csv
        $parser = CsvParser::create($reader);
        $languages = $parser->parse(false);

        //validate language codes
        $supportedLanguages = collect($crowdin->languageList())->map->getId();

        $csvLanguagesFull = collect($languages)->keys();
        foreach ($csvLanguagesFull as $item) {
            if (!$supportedLanguages->containsStrict($item)) {
                $command->error("Language '$item' not found in the supported languages list.");
                $command->line("Use the 'larex-crowdin:languages' command to get a list of supported languages.");

                $fuse = new Fuse(
                    $supportedLanguages->map(fn ($item) => ['code' => $item])->toArray(),
                    ['keys' => ['code']]
                );

                $suggest = collect($fuse->search($item))->pluck('item.code');

                if ($suggest->isNotEmpty()) {
                    $command->warn("Try to change '$item' code with one of these supported codes: {$suggest->implode(', ')}");
                }

                return 1;
            }
        }

        //get csv header
        $headers = $reader->getHeader();
        $csvSourceLanguage = $headers[2];

        if ($csvSourceLanguage !== $sourceLanguage) {
            $command->error(sprintf("Your CSV's 3rd language code column (%s) must match your Crowdin Project's source language code (%s)!",
                $csvSourceLanguage,
                $sourceLanguage
            ));

            return 1;
        }

        //get source file list
        /** @var Collection|File[] $fileList */
        $fileList = collect($crowdin->fileList($projectID));

        //upload source files
        $command->warn('Uploading source files...');
        $bar = $command->getOutput()->createProgressBar(count($languages[$csvSourceLanguage]));
        foreach ($languages[$csvSourceLanguage] as $group => $values) {
            $fileName = "$group.json";

            //build file to upload
            $fileToUpload = new VirtualSplFileObject($fileName, json_encode($values, JSON_THROW_ON_ERROR));

            //create storage file
            $storage = $crowdin->storage->create($fileToUpload);

            $file = $fileList->first(fn ($item) => $item->getName() === $fileName);

            if ($file === null) {
                //create source file
                $crowdin->file->create($projectID, [
                    'storageId' => $storage->getId(),
                    'name' => $fileName,
                ]);
            } else {
                //replace source file
                $crowdin->file->update($projectID, $file->getId(), [
                    'storageId' => $storage->getId(),
                ]);
            }

            $crowdin->storage->delete($storage->getId());

            $bar->advance();
        }
        $bar->finish();
        $command->info('');
        $command->info('');

        //delete source files if old groups doesn't exists anymore
        $csvGroups = collect($languages)->flatMap(fn ($item) => collect($item)->keys())->unique();

        foreach ($fileList as $file) {
            if (!$csvGroups->contains(str_replace('.json', '', $file->getName()))) {
                $crowdin->file->delete($projectID, $file->getId());
            }
        }

        //get target languages
        $csvLanguages = collect($languages)
            ->keys()
            ->when($include->isNotEmpty(), fn (Collection $c) => $c->intersect($include))
            ->when($exclude->isNotEmpty(), fn (Collection $c) => $c->diff($exclude))
            ->reject($csvSourceLanguage);

        //create/delete target languages
        $project->setTargetLanguageIds($csvLanguages->toArray());
        $project = $crowdin->project->update($project);
        $targetLanguages = collect($project->getTargetLanguageIds())->sort()->values();

        //upload already translated languages
        $command->warn('Uploading translation files...');
        $bar = $command->getOutput()->createProgressBar(count($targetLanguages));
        foreach ($targetLanguages as $languageID) {
            //loop source file (groups)
            foreach ($fileList as $itemFile) {
                $itemFileName = str_replace('.json', '', $itemFile->getName());

                if (!array_key_exists($itemFileName, $languages[$languageID])) {
                    continue;
                }

                //build file to upload
                $file = new VirtualSplFileObject(
                    $itemFile->getName(),
                    json_encode($languages[$languageID][$itemFileName], JSON_THROW_ON_ERROR)
                );

                $storage = $crowdin->storage->create($file);

                //upload file
                $crowdin->translation->uploadTranslations($projectID, $languageID, [
                    'storageId' => $storage->getId(),
                    'fileId' => $itemFile->getId(),
                    'importEqSuggestions' => true,
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
        $command->info('');
        $command->info('');

        $command->info('Export completed successfully.');

        return 0;
    }
}
