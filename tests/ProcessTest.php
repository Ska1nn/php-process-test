<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Model\Process;
use App\Model\Fields\TextField;
use App\Model\Fields\NumberField;
use App\Model\Fields\DateField;

final class ProcessTest extends TestCase
{
    public function testFields(): void
    {
        $p = new Process('Demo');
        $p->addField(new TextField('Text', 'Hello'));
        $p->addField(new NumberField('Number', 3.1415, ['format' => '%.2f']));
        $p->addField(new DateField('Start', '12.03.2023', ['format' => 'd.m.Y']));

        $arr = $p->toArray();
        $this->assertEquals('Demo', $arr['name']);
        $this->assertCount(3, $arr['fields']);
        $this->assertEquals('3.14', $arr['fields']['Number']['formatted']);
    }
    public function testProcessCanAddFields()
    {
        $p = new Process('Demo');
        $p->addField(new App\Model\Fields\TextField('username', 'admin'));

        $this->assertArrayHasKey('username', $p->getFields());
        $this->assertEquals('admin', $p->getFields()['username']->getValue());
    }
}
