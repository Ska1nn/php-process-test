<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Database\Connection;
use App\Database\Migration;
use App\Model\Process;
use App\Model\Fields\TextField;
use App\Model\Fields\NumberField;
use App\Model\Fields\DateField;
use App\Repository\ProcessRepository;

$pdo = Connection::getInstance();

$pdo->exec("DELETE FROM fields");
$pdo->exec("DELETE FROM processes");
echo "🧹 Таблицы processes и fields очищены!\n";

$migration = new Migration($pdo);
$migration->migrate();
echo "✅ Миграция выполнена успешно!\n\n";

$process = new Process('Test Process');
$process->addField(new TextField('name', 'John Doe'));
$process->addField(new NumberField('age', 29, ['format' => '%.2f']));
$process->addField(new DateField('registered_at', '2025-10-21', ['format' => 'd M Y']));

$repository = new ProcessRepository($pdo);
$repository->save($process);
echo "✅ Процесс сохранён с ID: {$process->getId()}\n\n";

$processes = $repository->getAll();
echo "📋 Список всех процессов:\n";
foreach ($processes as $p) {
    echo "- [{$p->getId()}] {$p->getName()}\n";
}

$loaded = $repository->findById($process->getId());

echo "\n📦 Подробности по процессу '{$loaded->getName()}':\n";
foreach ($loaded->getFields() as $field) {
    echo "  • {$field->getName()} ({$field->getType()}): {$field->formatValue()}\n";
}

echo "\n📄 JSON представление процесса:\n";
echo json_encode($loaded->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

echo "\n🚀 Демонстрация завершена успешно!\n";
