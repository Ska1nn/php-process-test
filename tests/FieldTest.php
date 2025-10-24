<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Model\Fields\{TextField, NumberField, DateField};

final class FieldTest extends TestCase
{
    public function testNumberFormatting(): void
    {
        $field = new NumberField('age', 29, '%.2f');
        $this->assertSame('29.00', $field->format());
    }

    public function testDateFormatting(): void
    {
        $field = new DateField('created_at', '2025-10-21', 'Y-m-d');
        $this->assertSame('2025-10-21', $field->format());
    }

    public function testTextFieldValue(): void
    {
        $field = new TextField('name', 'John Doe');
        $this->assertSame('John Doe', $field->format());
    }
}
