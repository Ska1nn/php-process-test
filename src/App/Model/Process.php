<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\Fields\FieldInterface;

class Process
{
    private ?int $id;
    private string $name;
    /** @var FieldInterface[] */
    private array $fields = [];

    public function __construct(string $name, ?int $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }

    public function addField(FieldInterface $field): void
    {
        if (isset($this->fields[$field->getName()])) {
            throw new \InvalidArgumentException("Field '{$field->getName()}' already exists.");
        }
        $this->fields[$field->getName()] = $field;
    }

    public function getAll(int $limit = 10, int $offset = 0, ?string $search = null): array
    {
        $query = "SELECT id, name FROM processes";
        $params = [];

        if ($search) {
            $query .= " WHERE name LIKE :search";
            $params['search'] = "%{$search}%";
        }

        $query .= " ORDER BY id ASC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);

        foreach ($params as $k => $v) {
            $stmt->bindValue(":$k", $v, PDO::PARAM_STR);
        }

        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        $result = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $p = new Process($row['name']);
            $reflection = new \ReflectionClass($p);
            $prop = $reflection->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($p, (int)$row['id']);
            $result[] = $p;
        }

        return $result;
    }

    public function getFields(): array { return $this->fields; }

    public function getField(string $name): ?FieldInterface
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }
        return null;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'fields' => array_map(fn($f) => $f->toArray() + ['formatted' => $f->formatValue()], $this->fields)
        ];
    }
}
