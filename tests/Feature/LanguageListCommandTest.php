<?php

use CrowdinApiClient\Api\LanguageApi;
use CrowdinApiClient\Model\Language;
use Lukasss93\LarexCrowdin\Commands\LanguagesListCommand;
use Mockery\MockInterface;

it('lists supported languages', function () {
    mockCrowdin([
        'language' => $this->mock(LanguageApi::class, function (MockInterface $mock) {
            $mock
                ->shouldReceive('list')
                ->andReturnValues(getStub([
                    'language-list/languagesFull.php',
                    'language-list/languagesEmpty.php',
                ], true));
        }),
    ]);

    $supportedLanguages = collect(getStub('language-list/languagesFull.php', true))
        ->map(fn (Language $item) => [$item->getId(), $item->getName()])
        ->sortBy(0)
        ->values()
        ->toArray();

    $this->artisan(LanguagesListCommand::class)
        ->expectsTable(['code', 'name'], $supportedLanguages)
        ->assertExitCode(0);
});
