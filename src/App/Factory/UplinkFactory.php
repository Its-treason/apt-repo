<?php

namespace ItsTreason\AptRepo\App\Factory;

use Storj\Uplink\Project;
use Storj\Uplink\Uplink;

class UplinkFactory
{
    public function __invoke(): Project
    {
        $accessString = getenv('STORJ_ACCESS_STRING');
        $access = Uplink::create()->parseAccess($accessString);

        $project = $access->openProject();

        $bucket = getenv('STORJ_BUCKET');
        $project->ensureBucket($bucket);

        return $project;
    }
}
