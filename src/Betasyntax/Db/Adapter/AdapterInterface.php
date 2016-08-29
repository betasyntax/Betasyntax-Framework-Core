<?php namespace Betasyntax\Db\Adapter;

use Betasyntax\Db\DatabaseConfig;

/**
 * Abstract interface
 */
interface AdapterInterface
{
    public function connect(DatabaseConfig $config);
    public function fetch($sql);
    public function execute($sql);
    public function columnMeta();
    public function columnCount();
}
