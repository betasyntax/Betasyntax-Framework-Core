<?php
// File: ./Db/Adapter/AdapterInterface.php
namespace Betasyntax\Db\Adapter;

use config\Database as DbConfig;
/**
 * Abstract interface
 */
interface AdapterInterface
{
    public function connect(DbConfig $config);
    public function fetch($sql);
    public function execute($sql);
    public function columnMeta();
    public function columnCount();
}
