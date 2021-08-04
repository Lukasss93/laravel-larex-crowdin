<?php

namespace Lukasss93\LarexCrowdin\Support\Crowdin;

class VirtualSplFileObject
{
    private string $name;
    private string $content;

    public function __construct(string $name, string $content)
    {
        $this->name = $name;
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
