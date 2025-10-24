<?php
declare(strict_types=1);

namespace App\Model\Fields;

interface FieldInterface
{
    public function getName(): string;
    public function getType(): string;
    public function getDefault(): mixed;
    public function setValue(mixed $value): void;
    public function getValue(): mixed;
    public function formatValue(): string;
    public function toArray(): array;
}
