<?php
declare(strict_types=1);

namespace Stratadox\Deserializer\Test\Unit\Fixture;

final class ChildWithoutPropertyAccess extends ParentWithPrivateProperty
{
    public static function onlyWriteAtConstruction(string $propertyValue): self
    {
        return new self($propertyValue);
    }

    public function equals(ChildWithoutPropertyAccess $other): bool
    {
        return $this->property() === $other->property();
    }
}
