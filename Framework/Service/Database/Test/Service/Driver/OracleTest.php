<?php
namespace X\Service\Database\Test\Service\Driver;
use X\Core\X;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Driver\Oracle;
use X\Service\Database\DatabaseException;
final class OracleTest extends TestCase {
    /** @var Oracle */
    private $driver = null;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    protected function setUp() {
        $config = X::system()->getConfiguration()->get('params')->get('OracleDriverConfig');
        $this->driver = new Oracle($config);
        
        $this->driver->exec('INSERT INTO "students" ("id","name","age") VALUES (1, \'michael\', 10)');
        $this->driver->exec('INSERT INTO "students" ("id","name","age") VALUES (2, \'lois\', 20)');
        $this->driver->exec('INSERT INTO "students" ("id","name","age") VALUES (3, \'lana\', 30)');
    }
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->driver->exec('TRUNCATE TABLE "students"');
        $this->driver = null;
    }
    
    /** test exec */
    public function test_exec() {
        $rowCount = $this->driver->exec('DELETE FROM "students" WHERE "name"=\'michael\'');
        $this->assertEquals(1, $rowCount);
        
        $this->driver->exec('TRUNCATE TABLE "students"');
        for ( $i=0; $i<10; $i++ ) {
            $query = 'INSERT INTO "students" ("id","name","age") VALUES (:id, :name, :age)';
            $params = array(
                ':id' => $i,
                ':name' => "stu-{$i}",
                ':age'  => $i
            );
            $rowCount = $this->driver->exec($query, $params);
            $this->assertEquals(1, $rowCount);
        }
    }
    
    /** test query */
    public function test_query() {
        $result = $this->driver->query('SELECT * FROM "students" where "name"=\'michael\'')->fetch();
        $this->assertEquals('michael', $result['name']);
    }
    
    /** test_getLastInsertId */
    public function test_getLastInsertId() {
        $this->expectException(DatabaseException::class);
        $this->driver->getLastInsertId();
    }
}