<?php
namespace X\Service\Database\Test\Service\Query;
use PHPUnit\Framework\TestCase;
use X\Service\Database\Database;
use X\Service\Database\Query\Delete;
use X\Service\Database\Test\Util\DatabaseServiceTestTrait;
use X\Service\Database\Query;
class DeleteTest extends TestCase {
    /** Uses */
    use DatabaseServiceTestTrait;
    
    /**
     * {@inheritDoc}
     * @see \PHPUnit\Framework\TestCase::tearDown()
     */
    protected function tearDown() {
        $this->cleanAllDatabase();
    }
    
    /**
     * @param unknown $dbName
     */
    private function doTestDelete( $dbName, $tableName ) {
        # delete all
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $delCount = Query::delete($dbName)->from($tableName)->exec();
        $this->assertEquals($insertCount, $delCount, 'failed to delete all');
        $this->dropTestTableUser($dbName);
        
        # delete one
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $deleteQuery = Query::delete($dbName);
        if ( 'postgresql' === $this->getDatabase($dbName)->getDriver()->getName() ) {
            $deleteQuery->setPrimaryKeyName('id');
        }
        $delCount = $deleteQuery->from($tableName)->limit(1)->exec();
        $rowCount = Query::select($dbName)->from($tableName)->all()->count();
        $this->assertEquals(1, $delCount, 'failed to delete one on assert $delCount');
        $this->assertEquals($insertCount-1, $rowCount, 'failed to delete one on assert $rowCount');
        $this->dropTestTableUser($dbName);
        
        # delete ten
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $deleteQuery = Query::delete($dbName);
        if ( 'postgresql' === $this->getDatabase($dbName)->getDriver()->getName() ) {
            $deleteQuery->setPrimaryKeyName('id');
        }
        $delCount = $deleteQuery->from($tableName)->limit(10)->exec();
        $this->assertEquals($insertCount, $delCount, 'failed to delete ten');
        $this->dropTestTableUser($dbName);
        
        # delete with condition
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $this->assertEquals(1, Query::select($dbName)->from($tableName)->where(['name'=>'U001-DM'])->all()->count());
        $deleteQuery = Query::delete($dbName);
        if ( 'postgresql' === $this->getDatabase($dbName)->getDriver()->getName() ) {
            $deleteQuery->setPrimaryKeyName('id');
        }
        $delCount = $deleteQuery->from($tableName)->where(['name'=>'U001-DM'])->exec();
        $this->assertEquals(1, $delCount, 'failed to delete with condition');
        $this->assertEquals(0, Query::select($dbName)->from($tableName)->where(['name'=>'U001-DM'])->all()->count());
        $this->dropTestTableUser($dbName);
        
        # delete with order
        $this->createTestTableUser($dbName);
        $insertCount = $this->insertDemoDataIntoTableUser($dbName);
        $minId = Query::select($dbName)->column('id')->from($tableName)->orderBy('id',SORT_ASC)->one();
        $deleteQuery = Query::delete($dbName);
        
        $driverName = $this->getDatabase($dbName)->getDriver()->getName();
        if ( 'postgresql' === $driverName || 'mssql' === $driverName ) {
            $deleteQuery->setPrimaryKeyName('id');
        }
        $delCount = $deleteQuery->from($tableName)->orderBy('id',SORT_ASC)->limit(1)->exec();
        $this->assertEquals(1, $delCount, 'failed to delete with order');
        $deletedMinId = Query::select($dbName)->column('id')->from($tableName)->orderBy('id',SORT_ASC)->one();
        $this->assertLessThan($deletedMinId['id'],$minId['id']);
        $this->dropTestTableUser($dbName);
    }
    
    /** */
    public function test_mysql() {
        $this->checkTestable(TEST_DB_NAME_MYSQL);
        $this->doTestDelete(TEST_DB_NAME_MYSQL, 'users');
    }
    
    /** */
    public function test_sqlite() {
        $this->checkTestable(TEST_DB_NAME_SQLITE);
        $this->doTestDelete(TEST_DB_NAME_SQLITE, 'users');
    }
    
    /** */
    public function test_postgresql() {
        $this->checkTestable(TEST_DB_NAME_POSTGRESQL);
        $this->doTestDelete(TEST_DB_NAME_POSTGRESQL, 'users');
    }
    
    /** */
    public function test_oracle() {
        $this->checkTestable(TEST_DB_NAME_ORACLE);
        $this->doTestDelete(TEST_DB_NAME_ORACLE, 'users');
    }
    
    /** */
    public function test_mssql() {
        $this->checkTestable(TEST_DB_NAME_MSSQL);
        $this->doTestDelete(TEST_DB_NAME_MSSQL, 'users');
    }
    
    /** */
    public function test_firebird() {
        $this->checkTestable(TEST_DB_NAME_FIREBIRD);
        $this->doTestDelete(TEST_DB_NAME_FIREBIRD, 'USERS');
    }
}