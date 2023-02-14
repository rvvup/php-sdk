<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests\Unit\GraphQlSdk;

use PHPUnit\Framework\TestCase;
use Rvvup\Sdk\GraphQlSdk;

class AbstractGraphQlSdkTestCase extends TestCase
{
    /**
     * @param $adapterStub
     * @return \Rvvup\Sdk\GraphQlSdk
     */
    protected function createGraphQlSdk($adapterStub): GraphQlSdk
    {
        return new GraphQlSdk(
            'https://endpoint.com/url',
            'MEXXXXXXX',
            'AUTH_TOKEN',
            'USER_AGENT',
            $adapterStub,
            null,
            false
        );
    }
}
