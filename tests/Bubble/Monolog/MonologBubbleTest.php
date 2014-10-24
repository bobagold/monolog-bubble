<?php


namespace Bubble\Monolog;


use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;

class MonologBubbleTest extends \PHPUnit_Framework_TestCase
{
    public function testDoctrineException()
    {
        $db = $this->db();
        $query = 'SELECT * FROM auth_users WHERE ok = ?';
        $ex = new \PDOException('SQLSTATE[42703]: Undefined column: 7 ERROR:  column "ok" does not exist
LINE 1: SELECT * FROM auth_users WHERE ok = $1
                                       ^');
        $first = DBALException::driverExceptionDuringQuery($ex, $query, $db->resolveParams(array(1), array()));
        $second = DBALException::driverExceptionDuringQuery($ex, $query, $db->resolveParams(array(2), array()));

        $firstContext = MonologBubble::exceptionContext($first);
        $secondContext = MonologBubble::exceptionContext($second);
        $this->assertNotEquals(
            $firstContext,
            $secondContext
        );
        $this->assertEquals(
            $this->loggerRecord($first),
            $this->loggerRecord($second)
        );
    }
    public function testDoctrineExceptionFailedTransaction()
    {
        $db = $this->db();
        $ex = new \PDOException('SQLSTATE[25P02]: In failed sql transaction: 7 ERROR:  current transaction is aborted, commands ignored until end of transaction block');
        $first = DBALException::driverExceptionDuringQuery($ex, 'SELECT * FROM auth_users WHERE ok = ?', $db->resolveParams(array(1), array()));
        $second = DBALException::driverExceptionDuringQuery($ex, 'SELECT * FROM product WHERE id = ?', $db->resolveParams(array(2), array()));

        $firstContext = MonologBubble::exceptionContext($first);
        $secondContext = MonologBubble::exceptionContext($second);
        $this->assertNotEquals(
            $firstContext,
            $secondContext
        );
        $this->assertEquals(
            $this->loggerRecord($first),
            $this->loggerRecord($second)
        );
    }

    public function testDoctrineExceptionConnectionFailed()
    {
        $db = $this->badDb();
        try {
            $db->fetchAssoc('SELECT * FROM auth_users WHERE ok = ?', array(1));
        } catch (\PDOException $first) {
            try {
                $db->fetchAssoc('SELECT * FROM auth_users WHERE ok = ?', array(2));
            } catch (\PDOException $second) {
                $firstContext = MonologBubble::exceptionContext($first);
                $secondContext = MonologBubble::exceptionContext($second);
                $this->assertNotEquals(
                    $firstContext,
                    $secondContext
                );
                $this->assertEquals(
                    $this->loggerRecord($first),
                    $this->loggerRecord($second)
                );
            }
        }
    }

    private function loggerRecord($exceptionContext)
    {
        return MonologBubble::fingerPrint(array(
            'message' => 'Unhandled exception',
            'datetime' => new \DateTime(),
            'context' => array('exception' => $exceptionContext),
        ));
    }

    private function db()
    {
        return DriverManager::getConnection(
            array(
                'driver' => 'pdo_sqlite',
                'memory' => true,
            )
        );
    }

    private function badDb()
    {
        return DriverManager::getConnection(
            array(
                'driver' => 'pdo_oci',
                'dbname' => 'not_existent'
            )
        );
    }
}