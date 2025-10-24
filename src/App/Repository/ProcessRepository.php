<?php
declare(strict_types=1);

namespace App\Repository;

use App\Model\Process;
use App\Model\Fields\{TextField, NumberField, DateField, FieldInterface};
use PDO;

class ProcessRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function clear(): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->exec("DELETE FROM fields");
            $this->pdo->exec("DELETE FROM processes");
            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function save(Process $process): int
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("INSERT INTO processes (name) VALUES (:name)");
            $stmt->execute(['name' => $process->getName()]);
            $id = (int) $this->pdo->lastInsertId();
            $this->setId($process, $id);

            foreach ($process->getFields() as $field) {
                $this->saveField($id, $field);
            }

            $this->pdo->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function saveField(int $processId, FieldInterface $field): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO fields (process_id, name, type, value, format)
            VALUES (:process_id, :name, :type, :value, :format)
        ");
        $stmt->execute([
            'process_id' => $processId,
            'name'       => $field->getName(),
            'type'       => $field->getType(),
            'value'      => (string)$field->getValue(),
            'format'     => $field->getFormattedValue(),
        ]);
    }

    public function getAll(int $limit = 10, int $offset = 0, ?string $search = null): array
    {
        $sql = "SELECT id, name FROM processes";
        $params = [];

        if ($search) {
            $sql .= " WHERE name LIKE :search";
            $params[':search'] = "%{$search}%";
        }

        $sql .= " ORDER BY id ASC LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $processes = [];
        foreach ($rows as $row) {
            $process = new Process($row['name']);
            $this->setId($process, (int)$row['id']);
            $this->loadFields($process);
            $processes[] = $process;
        }
        return $processes;
    }

    public function findById(int $id): ?Process
    {
        $stmt = $this->pdo->prepare("SELECT * FROM processes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $processData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$processData) {
            return null;
        }

        $process = new Process($processData['name']);
        $this->setId($process, (int)$processData['id']);
        $this->loadFields($process);

        return $process;
    }

    private function loadFields(Process $process): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM fields WHERE process_id = :id");
        $stmt->execute(['id' => $process->getId()]);
        $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($fields as $f) {
            switch ($f['type']) {
                case 'number':
                    $process->addField(
                        new NumberField($f['name'], (float)$f['value'], ['format' => $f['format']])
                    );
                    break;
                case 'date':
                    $process->addField(
                        new DateField($f['name'], $f['value'], ['format' => $f['format']])
                    );
                    break;
                default:
                    $process->addField(
                        new TextField($f['name'], $f['value'])
                    );
            }
        }
    }

    private function setId(Process $process, int $id): void
    {
        $reflection = new \ReflectionClass($process);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($process, $id);
    }
}
