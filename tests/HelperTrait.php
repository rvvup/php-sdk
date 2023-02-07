<?php

declare(strict_types=1);

namespace Rvvup\Sdk\Tests;

/**
 * Helper trait to be used across testing functions as an easier replacement of Faker.
 * Faker\Faker has been archived & PHPFaker is available for PHP versions >=7.4 and also has a large footprint.
 */
trait HelperTrait
{
    /**
     * @param int|null $length
     * @return string
     * @throws \Exception
     */
    public function getRandomString(?int $length = null): string
    {
        return bin2hex(random_bytes($length ?? 6));
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getRandomNumber(): int
    {
        return random_int(1, 100);
    }
}
