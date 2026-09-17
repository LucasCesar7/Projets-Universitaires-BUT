<?php

declare(strict_types=1);

namespace Tests\Unit\Config;

use Tests\TestCase;

class PathsTest extends TestCase
{
    public function testResolveBaseUrlStripsPublicFrontControllerPath(): void
    {
        $this->assertSame('/project', resolveBaseUrlFromServerPath('/project/public/index.php'));
    }

    public function testResolveBaseUrlStripsLegacyViewPath(): void
    {
        $this->assertSame('/project', resolveBaseUrlFromServerPath('/project/views/login.php'));
    }
}