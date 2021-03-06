<?php
declare(strict_types=1);

namespace Stratadox\Deserializer\Test\Unit;

use Stratadox\Deserializer\DeserializationFailure;
use Stratadox\Deserializer\DeserializationOption;
use function array_combine;
use Faker\Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use function shuffle;
use Stratadox\Deserializer\OneOfThese;
use Stratadox\Deserializer\Test\Unit\Fixture\Popo;

class OneOfThese_deserializers_will_handle_the_input extends TestCase
{
    /**
     * @test
     * @dataProvider withExpectedOutput
     */
    function producing_the_expected_output(
        array $inputData,
        array $deserializationChoices,
        $expectedResult
    ) {
        self::assertEquals(
            $expectedResult,
            OneOfThese::deserializers(
                ...$deserializationChoices
            )->from($inputData)
        );
    }

    /**
     * @test
     * @dataProvider withExpectedType
     */
    function producing_the_expected_type(
        array $inputData,
        array $deserializationChoices,
        string $expectedType
    ) {
        self::assertSame(
            $expectedType,
            OneOfThese::deserializers(
                ...$deserializationChoices
            )->typeFor($inputData)
        );
    }

    /** @test */
    function trying_to_deserialize_invalid_input()
    {
        $deserializer = OneOfThese::deserializers(
            $this->unexpectedChoice(),
            $this->unexpectedChoice()
        );

        $this->expectException(DeserializationFailure::class);
        $this->expectExceptionMessage(
            'None of the deserializers are configured to accept `{"foo":"bar"}`'
        );

        $deserializer->from(['foo' => 'bar']);
    }

    /** @test */
    function trying_to_determine_the_type_of_invalid_input()
    {
        $deserializer = OneOfThese::deserializers(
            $this->unexpectedChoice(),
            $this->unexpectedChoice()
        );

        $this->expectException(DeserializationFailure::class);
        $this->expectExceptionMessage(
            'None of the deserializers are configured to accept `{"foo":"bar"}`.'
        );

        $deserializer->typeFor(['foo' => 'bar']);
    }

    public function withExpectedOutput(): array
    {
        $random = Factory::create();
        $n = $random->numberBetween(0, 10);
        $expectedOutput = new Popo;
        $choices = [
            $this->expectedChoice('from', $expectedOutput),
            $this->unexpectedChoice(),
            $this->unexpectedChoice(),
            $this->unexpectedChoice(),
        ];
        shuffle($choices);
        return [
            "$n random words" => [
                [array_combine($random->words($n), $random->words($n))],
                $choices,
                $expectedOutput
            ]
        ];
    }

    public function withExpectedType(): array
    {
        $random = Factory::create();
        $n = $random->numberBetween(0, 10);
        $expectedOutput = Popo::class;
        $choices = [
            $this->expectedChoice('typeFor', $expectedOutput),
            $this->unexpectedChoice(),
            $this->unexpectedChoice(),
            $this->unexpectedChoice(),
        ];
        shuffle($choices);
        return [
            "$n random words" => [
                [array_combine($random->words($n), $random->words($n))],
                $choices,
                $expectedOutput
            ]
        ];
    }

    private function expectedChoice(string $expectation, $output): DeserializationOption
    {
        /** @var DeserializationOption|MockObject $option */
        $option = $this->createMock(DeserializationOption::class);
        $option->expects(self::once())
            ->method('isSatisfiedBy')
            ->willReturn(true);
        $option
            ->expects(self::once())
            ->method($expectation)
            ->willReturn($output);
        return $option;
    }

    private function unexpectedChoice(): DeserializationOption
    {
        /** @var DeserializationOption|MockObject $option */
        $option = $this->createMock(DeserializationOption::class);
        $option->method('isSatisfiedBy')->willReturn(false);
        return $option;
    }
}
