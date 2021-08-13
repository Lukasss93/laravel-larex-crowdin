<?php

use CrowdinApiClient\Api\FileApi;
use CrowdinApiClient\Api\LanguageApi;
use CrowdinApiClient\Api\ProjectApi;
use CrowdinApiClient\Api\TranslationApi;
use Lukasss93\Larex\Console\LarexExportCommand;
use Lukasss93\LarexCrowdin\Exporters\CrowdinExporter;
use Lukasss93\LarexCrowdin\Support\Crowdin\Crowdin;
use Lukasss93\LarexCrowdin\Support\Crowdin\StorageApi;
use Mockery\MockInterface;

beforeEach(function () {
    config([
        'larex.exporters.list' => array_merge(config('larex.exporters.list'), [
            'crowdin' => CrowdinExporter::class,
        ]),
    ]);
});

it('has right description', function () {
    expect(CrowdinExporter::description())
        ->toEqual('Export data from CSV to a Crowdin project');
});

it('does not export strings due to invalid language code', function () {

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/invalid-language-code/project.php', true));
        }),
        'language' => $this->partialMock(LanguageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/common/languagesFull.php',
                    'exporter/common/languagesEmpty.php',
                ], true));
        }),
    ]);

    initFromTestStub('exporter/invalid-language-code/localization.csv');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'crowdin'])
        ->expectsOutput("Processing the 'resources/lang/localization.csv' file...")
        ->expectsOutput('Getting project informations...')
        ->expectsOutput('Project: [123456] Foo Project')
        ->expectsOutput('Source language: en')
        ->expectsOutput("Language 'es-ESOOO' not found in the supported languages list.")
        ->expectsOutput("Use the 'larex-crowdin:languages' command to get a list of supported languages.")
        ->expectsOutput("Try to change 'es-ESOOO' code with one of these supported codes: es-ES, es-EM, es-BO, es-CO, es-DO, es-EC, es-SV, es-US")
        ->assertExitCode(1);
});

it('does not export strings due to unmatching source language code', function () {

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/unmatching-source-language-code/project.php', true));
        }),
        'language' => $this->partialMock(LanguageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/common/languagesFull.php',
                    'exporter/common/languagesEmpty.php',
                ], true));
        }),
    ]);

    initFromTestStub('exporter/unmatching-source-language-code/localization.csv');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'crowdin'])
        ->expectsOutput("Processing the 'resources/lang/localization.csv' file...")
        ->expectsOutput('Getting project informations...')
        ->expectsOutput('Project: [123456] Foo Project')
        ->expectsOutput('Source language: de')
        ->expectsOutput("Your CSV's 3rd language code column (en) must match your Crowdin Project's source language code (de)!")
        ->assertExitCode(1);

});

it('exports strings', function () {

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/base/project.php', true));
            $mock
                ->shouldReceive('update')
                ->andReturn(getStub('exporter/base/projectUpdated.php', true));
        }),
        'file' => $this->partialMock(FileApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/base/sourceFilesListFull.php',
                    'exporter/base/sourceFilesListEmpty.php',
                ], true));
            $mock
                ->shouldReceive('create')
                ->andReturn();
            $mock
                ->shouldReceive('update')
                ->andReturn();
            $mock
                ->shouldReceive('delete')
                ->andReturn();
        }),
        'storage' => $this->partialMock(StorageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('create')
                ->andReturn(getStub('exporter/base/storage.php', true));
            $mock
                ->shouldReceive('delete')
                ->andReturn();
        }),
        'translation' => $this->partialMock(TranslationApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('uploadTranslations')
                ->andReturn();
        }),
        'language' => $this->partialMock(LanguageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/common/languagesFull.php',
                    'exporter/common/languagesEmpty.php',
                ], true));
        }),
    ]);

    initFromTestStub('exporter/base/localization.csv');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'crowdin'])
        ->expectsOutput("Processing the 'resources/lang/localization.csv' file...")
        ->expectsOutput('Getting project informations...')
        ->expectsOutput('Project: [123456] Foo Project')
        ->expectsOutput('Source language: en')
        ->expectsOutput('Uploading source files...')
        ->expectsOutput('Uploading translation files...')
        ->expectsOutput('Export completed successfully.')
        ->assertExitCode(0);

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/base/projectUpdated.php', true));
        }),
    ]);

    /** @var Crowdin $crowdin */
    $crowdin = app(Crowdin::class);
    $project = $crowdin->project->get(123456);

    expect($project->getTargetLanguageIds())->toEqual(['es-ES', 'it', 'fr']);
});

it('exports strings with --include option', function () {
    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/base/project.php', true));
            $mock
                ->shouldReceive('update')
                ->andReturn(getStub('exporter/base/projectInclude.php', true));
        }),
        'file' => $this->partialMock(FileApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/base/sourceFilesListFull.php',
                    'exporter/base/sourceFilesListEmpty.php',
                ], true));
            $mock
                ->shouldReceive('create')
                ->andReturn();
            $mock
                ->shouldReceive('update')
                ->andReturn();
            $mock
                ->shouldReceive('delete')
                ->andReturn();
        }),
        'storage' => $this->partialMock(StorageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('create')
                ->andReturn(getStub('exporter/base/storage.php', true));
            $mock
                ->shouldReceive('delete')
                ->andReturn();
        }),
        'translation' => $this->partialMock(TranslationApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('uploadTranslations')
                ->andReturn();
        }),
        'language' => $this->partialMock(LanguageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/common/languagesFull.php',
                    'exporter/common/languagesEmpty.php',
                ], true));
        }),
    ]);

    initFromTestStub('exporter/base/localization.csv');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'crowdin', '--include' => 'it'])
        ->expectsOutput("Processing the 'resources/lang/localization.csv' file...")
        ->expectsOutput('Getting project informations...')
        ->expectsOutput('Project: [123456] Foo Project')
        ->expectsOutput('Source language: en')
        ->expectsOutput('Uploading source files...')
        ->expectsOutput('Uploading translation files...')
        ->expectsOutput('Export completed successfully.')
        ->assertExitCode(0);

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/base/projectInclude.php', true));
        }),
    ]);

    /** @var Crowdin $crowdin */
    $crowdin = app(Crowdin::class);
    $project = $crowdin->project->get(123456);

    expect($project->getTargetLanguageIds())->toEqual(['it']);
});

it('exports strings with --exclude option', function () {
    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/base/project.php', true));
            $mock
                ->shouldReceive('update')
                ->andReturn(getStub('exporter/base/projectExclude.php', true));
        }),
        'file' => $this->partialMock(FileApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/base/sourceFilesListFull.php',
                    'exporter/base/sourceFilesListEmpty.php',
                ], true));
            $mock
                ->shouldReceive('create')
                ->andReturn();
            $mock
                ->shouldReceive('update')
                ->andReturn();
            $mock
                ->shouldReceive('delete')
                ->andReturn();
        }),
        'storage' => $this->partialMock(StorageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('create')
                ->andReturn(getStub('exporter/base/storage.php', true));
            $mock
                ->shouldReceive('delete')
                ->andReturn();
        }),
        'translation' => $this->partialMock(TranslationApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('uploadTranslations')
                ->andReturn();
        }),
        'language' => $this->partialMock(LanguageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'exporter/common/languagesFull.php',
                    'exporter/common/languagesEmpty.php',
                ], true));
        }),
    ]);

    initFromTestStub('exporter/base/localization.csv');

    $this->artisan(LarexExportCommand::class, ['exporter' => 'crowdin', '--exclude' => 'it,fr'])
        ->expectsOutput("Processing the 'resources/lang/localization.csv' file...")
        ->expectsOutput('Getting project informations...')
        ->expectsOutput('Project: [123456] Foo Project')
        ->expectsOutput('Source language: en')
        ->expectsOutput('Uploading source files...')
        ->expectsOutput('Uploading translation files...')
        ->expectsOutput('Export completed successfully.')
        ->assertExitCode(0);

    mockCrowdin([
        'project' => $this->partialMock(ProjectApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('get')
                ->andReturn(getStub('exporter/base/projectExclude.php', true));
        }),
    ]);

    /** @var Crowdin $crowdin */
    $crowdin = app(Crowdin::class);
    $project = $crowdin->project->get(123456);

    expect($project->getTargetLanguageIds())->toEqual(['es-ES']);
});
