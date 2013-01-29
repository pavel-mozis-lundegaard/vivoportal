<?php
namespace Vivo\Controller\CLI;

use Zend\Db\Adapter\Adapter as ZendDbAdapter;

/**
 * Vivo CLI controller for command 'setup'
 */
class SetupController extends AbstractCliController
{
    const COMMAND = 'setup';

    /**
     * Zend DB adapter
     * @var ZendDbAdapter
     */
    protected $zdba;

    /**
     * Constructor
     * @param \Zend\Db\Adapter\Adapter $zdba
     */
    public function __construct(ZendDbAdapter $zdba)
    {
        $this->zdba = $zdba;
    }

    public function getConsoleUsage()
    {
        $output = "\nSetup usage:";
        $output .= "\n\nsetup db";
        return $output;
    }

    /**
     * Sets up database
     * @return string
     */
    public function dbAction()
    {
        $ddl        = "CREATE TABLE IF NOT EXISTS `users` (
                      `domain` varchar(60) NOT NULL DEFAULT 'GLOBAL',
                      `username` varchar(30) NOT NULL,
                      `password` varchar(100),
                      `fullname` varchar(50),
                      `email` varchar(60),
                      `active` tinyint(1) NOT NULL DEFAULT 1,
                      `expires` datetime,
                      PRIMARY KEY (`domain`, `username`));";
        $this->zdba->query($ddl, ZendDbAdapter::QUERY_MODE_EXECUTE);
        $output = 'Table users created';

//        $sql    = new \Zend\Db\Sql\Sql($dba, $tableName);
//        $insert = $sql->insert();
//        $insert->columns(array('site', 'first_name', 'last_name'));
//        $insert->values(array($siteName, 'Jiří', 'Šťastný'));
//        $statement  = $sql->prepareStatementForSqlObject($insert);
//        $result     = $statement->execute();
//        $insert->values(array($siteName, 'Bohuslav', 'Smělý'));
//        $statement  = $sql->prepareStatementForSqlObject($insert);
//        $result     = $statement->execute();

//        $output = "Attempted deletion of all documents from index";
        return $output;
    }
}
