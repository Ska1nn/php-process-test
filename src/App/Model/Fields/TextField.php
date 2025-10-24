<?php
declare(strict_types=1);

namespace App\Model\Fields;

class TextField extends AbstractField
{
    public function __construct(
        string $name,
        mixed $value = null,
        mixed $default = null,
        array $extra = []
    ) {
        parent::__construct($name, 'text', $value, null, $default, $extra);
    }

    public function formatValue(): string
    {
        return (string)($this->value ?? $this->default ?? '');
    }
}
