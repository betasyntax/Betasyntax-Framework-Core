<?php namespace Betasyntax\DebugBar;

use DebugBar\DataCollector\PDO\PDOCollector;

Class DbCollector extends PDOCollector
{
    public function __construct()
    {
        parent::__construct();
        $this->addConnection($this->getTraceablePdo(), 'Betasyntax PDO');
    }

    /**
     * @return \DebugBar\DataCollector\PDO\TraceablePDO
     */
    protected function getTraceablePdo() {
        return new \DebugBar\DataCollector\PDO\TraceablePDO(app()->pdo);
    }

    // Override
    public function collect()
    {
        $queries = array();
        $totalExecTime = 0;
        if(is_array(app()->pdo_queries)) {
            foreach (app()->pdo_queries as $q) {
                list($query, $duration, $caller) = $q;
                $queries[] = array(
                    'sql' => $query,
                    'duration' => $duration,
                    'duration_str' => $this->formatDuration($duration)
                );
                $totalExecTime += $duration;
            }
        }

        return array(
            'nb_statements' => count($queries),
            'accumulated_duration' => $totalExecTime,
            'accumulated_duration_str' => $this->formatDuration($totalExecTime),
            'statements' => $queries
        );
    }

    // Override
    public function getName() {
        return "betasyntax_pdo";
    }

    public function getWidgets()
    {
        return array(
            "database" => array(
                "icon" => "arrow-right",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "betasyntax_pdo",
                "default" => "[]"
            ),
            "database:badge" => array(
                "map" => "betasyntax_pdo.nb_statements",
                "default" => 0
            )
        );
    }
}