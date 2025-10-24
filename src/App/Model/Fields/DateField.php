<?php
declare(strict_types=1);

namespace App\Model\Fields;

use DateTime;

class DateField extends AbstractField
{
    public function __construct(string $name, mixed $default = null, array $extra = [])
    {
        parent::__construct($name, 'date', $default, $extra);
    }

    public function formatValue(): string
    {
        $format = $this->extra['format'] ?? 'Y-m-d';
        if (!$this->value) return '';
        
        $dt = DateTime::createFromFormat('d.m.Y', (string)$this->value)
            ?: DateTime::createFromFormat('Y-m-d', (string)$this->value)
            ?: DateTime::createFromFormat('Y-m-d H:i:s', (string)$this->value);

        if (!$dt) return (string)$this->value;

        return $dt->format($format);
    }
    public function getFormattedValue(): string
    {
        try {
            $date = new \DateTime($this->value);
        } catch (\Exception $e) {
            return (string)$this->value;
        }

        if (!empty($this->options['format'])) {
            return $date->format($this->options['format']);
        }

        return $date->format('d.m.Y');
    }
}
