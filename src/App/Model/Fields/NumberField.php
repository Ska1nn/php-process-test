<?php
declare(strict_types=1);

namespace App\Model\Fields;

class NumberField extends AbstractField
{
    public function __construct(string $name, mixed $default = 0, array $extra = [])
    {
        parent::__construct($name, 'number', $default, $extra);
    }

    public function getType(): string
    {
        return 'number';
    }
    
    public function formatValue(): string
    {
        $format = $this->extra['format'] ?? '%f';
        return sprintf($format, (float)$this->value);
    }

    public function getFormattedValue(): string
    {
        if (!empty($this->options['format'])) {
            return sprintf($this->options['format'], $this->value);
        }

        return number_format((float)$this->value, 2, '.', '');
    }
}
