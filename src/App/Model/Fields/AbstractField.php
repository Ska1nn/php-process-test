<?php
declare(strict_types=1);

namespace App\Model\Fields;

abstract class AbstractField implements FieldInterface
{
    protected string $name;
    protected string $type;
    protected mixed $value;
    protected mixed $default;
    protected mixed $format; // теперь может быть строка, массив и т.д.
    protected array $extra;

    public function __construct(
        string $name,
        string $type,
        mixed $value = null,
        mixed $default = null,
        mixed $format = null,
        array $extra = []
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->value = $value ?? $default;
        $this->default = $default;
        $this->format = $format;
        $this->extra = $extra;
    }

    public function getName(): string { return $this->name; }
    public function getType(): string { return $this->type; }
    public function getValue(): mixed { return $this->value; }
    public function setValue(mixed $value): void { $this->value = $value; }
    public function getDefault(): mixed { return $this->default; }
    public function getFormat(): mixed { return $this->format; }
    public function getExtra(): array { return $this->extra; }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'default' => $this->default,
            'format' => $this->format,
            'extra' => $this->extra,
        ];
    }

    abstract public function formatValue(): string;

    public function getFormattedValue(): string
    {
        return $this->formatValue();
    }
}
