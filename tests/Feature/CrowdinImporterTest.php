<?php

use CrowdinApiClient\Api\FileApi;
use CrowdinApiClient\Api\ProjectApi;
use CrowdinApiClient\Api\TranslationApi;
use Illuminate\Support\Facades\Http;
use Lukasss93\Larex\Console\LarexImportCommand;
use Lukasss93\LarexCrowdin\Importers\CrowdinImporter;
use Mockery\MockInterface;

beforeEach(function () {
    config([
        'larex.importers.list' => array_merge(config('larex.importers.list'), [
            'crowdin' => CrowdinImporter::class,
        ]),
    ]);
});

it('has right description', function () {
    expect(CrowdinImporter::description())
        ->toEqual('Import data from a Crowdin project to CSV');
});

it('import strings from crowdin', function () {

    Http::fake([
        'crowdin-importer.downloads.crowdin.com/*' => Http::sequence()
            ->push(['hello' => 'Hello', 'car' => 'Car'])
            ->push(['red' => 'Red', 'blue' => 'Blue'])
            ->push(['phone' => 'Phone'])
            ->push(['one' => 'One'])
            ->whenEmpty(Http::response()),
        'crowdin-tmp.downloads.crowdin.com/*' => Http::response(getStub('importer/translationFile_it.xliff')),
    ]);

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('importer/project.php', true));
        }),
        'file' => $this->partialMock(FileApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub(['importer/sourceFilesListFull.php','importer/sourceFilesListEmpty.php'], true));
            $mock
                ->shouldReceive('download')
                ->andReturn(getStub('importer/sourceFileDownload.php', true));
        }),
        'translation' => $this->partialMock(TranslationApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('exportProjectTranslation')
                ->andReturn(getStub('importer/translationFileDownload.php', true));
        }),
    ]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'crowdin'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Getting project informations...')
        ->expectsOutput("Project: [123456] Foo Project")
        ->expectsOutput("Source language: en")
        ->expectsOutput("Target languages: 1")
        ->expectsOutput('Getting project source files list...')
        ->expectsOutput('Project source files found: 4')
        ->expectsOutput('Downloading project translation files...')
        ->expectsOutput('Downloading project source files...')
        ->expectsOutput('Data imported successfully.')
        ->assertExitCode(0);

    expect(base_path(config('larex.csv.path')))
        ->toBeFile()
        ->fileContent()
        ->toEqualStub('importer/localization.csv');

});

it('does not import due to no source files found', function () {

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('importer/project.php', true));
        }),
        'file' => $this->partialMock(FileApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturn(getStub('importer/sourceFilesListEmpty.php', true));
        }),
    ]);

    $this->artisan(LarexImportCommand::class, ['importer' => 'crowdin'])
        ->expectsOutput('Importing entries...')
        ->expectsOutput('Getting project informations...')
        ->expectsOutput("Project: [123456] Foo Project")
        ->expectsOutput("Source language: en")
        ->expectsOutput("Target languages: 1")
        ->expectsOutput('Getting project source files list...')
        ->expectsOutput('Project source files found: 0')
        ->expectsOutput('No data found to import.')
        ->assertExitCode(0);

    expect(base_path(config('larex.csv.path')))
        ->not()->toBeFile();

});
