<?php
// File: ./Db/Adapter/AdapterInterface.php
namespace Betasyntax\Db\Adapter;
/**
 * Abstract interface
 */
interface AdapterInterface
{
    public function connect(\config\DatabaseConfig $config);
    public function fetch($sql);
    public function execute($sql);
    public function columnMeta();
    public function columnCount();
}
