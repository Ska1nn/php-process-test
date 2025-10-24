<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Database\Connection;
use App\Database\Migration;
use App\Model\Process;
use App\Model\Fields\TextField;
use App\Model\Repository\ProcessRepository;

final class RepositoryTest extends TestCase
{
    private ProcessRepository $repo;

    protected function setUp(): void
    {
        $pdo = Connection::getInstance();
        (new Migration())->migrate();
        $this->repo = new ProcessRepository($pdo);
        $this->repo->clear();
    }

    public function testSaveAndFind(): void
    {
        $p = new Process('My Process');
        $p->addField(new TextField('name', 'Test'));
        $id = $this->repo->save($p);

        $found = $this->repo->findById($id);
        $this->assertNotNull($found);
        $this->assertEquals('My Process', $found->getName());
    }

    public function testSearchAndPagination(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $p = new Process("Process $i");
            $this->repo->save($p);
        }

        $results = $this->repo->getAll(limit: 2, offset: 1, search: 'Process');
        $this->assertCount(2, $results);
        $this->assertStringContainsString('Process', $results[0]->getName());
    }
}
