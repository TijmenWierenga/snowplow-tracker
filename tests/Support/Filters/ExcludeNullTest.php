<?php

declare(strict_types=1);

namespace TijmenWierenga\Tests\SnowplowTrackers\Support\Filters;

use TijmenWierenga\SnowplowTracker\Support\Filters\ExcludeNull;
use PHPUnit\Framework\TestCase;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
final class ExcludeNullTest extends TestCase
{
    public function testItShouldFilterNullValuesFromARegularArray(): void
    {
        // Given I have a regular array with null values
        $array = [
            true,
            false,
            1,
            1.5,
            '',
            'test',
            [],
            [1],
            null,
        ];

        // When I filter it to remove null values
        $result = array_filter($array, new ExcludeNull());

        // I expect the result to not contain any null values
        self::assertEquals(
            [
                true,
                false,
                1,
                1.5,
                '',
                'test',
                [],
                [1],
            ],
            $result
        );
    }

    public function testItShouldFilterNullValuesFromAnAssociativeArray(): void
    {
        // Given I have an associative array with null values
        $array = [
            'true' => true,
            'false' => false,
            'integer' => 1,
            'float' => 1.5,
            'emptyString' => '',
            'string' => 'string',
            'emptyArray' => [],
            'array' => [1],
            'null' => null
        ];

        // When I filter it to remove null values
        $result = array_filter($array, new ExcludeNull());

        // I expect the result to not contain any null values
        self::assertEquals(
            [
                'true' => true,
                'false' => false,
                'integer' => 1,
                'float' => 1.5,
                'emptyString' => '',
                'string' => 'string',
                'emptyArray' => [],
                'array' => [1],
            ],
            $result
        );
    }
}
