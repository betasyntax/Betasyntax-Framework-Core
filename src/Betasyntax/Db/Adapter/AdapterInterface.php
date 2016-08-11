<?php namespace Betasyntax\Db\Adapter;

use Betasyntax\Database;

/**
 * Abstract interface
 */
interface AdapterInterface
{
    public function connect(Database $config);
    public function fetch($sql);
    public function execute($sql);
    public function columnMeta();
    public function columnCount();
}
