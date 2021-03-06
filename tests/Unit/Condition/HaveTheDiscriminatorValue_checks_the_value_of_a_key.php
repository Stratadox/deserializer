<?php
declare(strict_types=1);

namespace Stratadox\Deserializer\Test\Unit\Condition;

use Faker\Factory as RandomGenerator;
use PHPUnit\Framework\TestCase;
use Stratadox\Deserializer\Condition\HaveTheDiscriminatorValue;

class HaveTheDiscriminatorValue_checks_the_value_of_a_key extends TestCase
{
    /**
     * @test
     * @dataProvider acceptedData
     */
    function check_that_the_right_value_gets_accepted(
        string $key,
        string $value,
        array $input
    ) {
        $constraint = HaveTheDiscriminatorValue::of($key, $value);
        self::assertTrue($constraint->isSatisfiedBy($input));
    }

    /**
     * @test
     * @dataProvider deniedData
     */
    function check_that_the_wrong_value_gets_denied(
        string $key,
        string $value,
        array $input
    ) {
        $constraint = HaveTheDiscriminatorValue::of($key, $value);
        self::assertFalse($constraint->isSatisfiedBy($input));
    }

    public function acceptedData(): array
    {
        $random = RandomGenerator::create();
        $key = $random->word;
        $value = $random->word;
        return [
            "Column $key with $value" => [
                $key,
                $value,
                [$key => $value],
            ],
            'Column n with 1' => [
                'n',
                '1',
                ['n' => 1],
            ],
        ];
    }

    public function deniedData(): array
    {
        $random = RandomGenerator::create()->unique();
        $key = $random->word;
        $expected = $random->word;
        $actual = $random->word;
        return [
            "Column $key expecting $expected but getting $actual" => [
                $key,
                $expected,
                [$key => $actual],
            ],
            'Column n expecting 4 but getting 3' => [
                'n',
                '4',
                ['n' => 3],
            ],
        ];
    }
}
