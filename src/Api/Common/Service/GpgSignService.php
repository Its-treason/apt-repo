<?php

namespace ItsTreason\AptRepo\Api\Common\Service;

class GpgSignService
{
    public function createReleaseGpg(string $releaseFile): string
    {
        return shell_exec(sprintf('echo "%s" | gpg --default-key -abs', $releaseFile));
    }

    public function createInRelease(string $releaseFile): string
    {
        return shell_exec(sprintf('echo "%s" | gpg --default-key -abs --clearsign', $releaseFile));
    }

    public function getPublicKey(): string
    {
        return shell_exec('gpg --armor --export');
    }
}
