<?php

namespace KinetiseSkeleton\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;

class Logger implements SQLLogger
{
    private $sql;
    private $params;
    private $types;
    private $startTime;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Logs a SQL statement somewhere.
     *
     * @param string $sql The SQL to be executed.
     * @param array|null $params The SQL parameters.
     * @param array|null $types The SQL parameter types.
     *
     * @return void
     */
    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->sql = $sql;
        $this->params = $params;
        $this->types = $types;
        $this->startTime = microtime(true);
    }

    /**
     * Marks the last started query as stopped. This can be used for timing of queries.
     *
     * @return void
     */
    public function stopQuery()
    {
        $this->logger->info(
            sprintf('Doctrine: %s', $this->sql),
            array(
                'executionTime' => number_format((microtime(true) - $this->startTime), 5),
                'params' => $this->params,
                'types' => $this->types
            )
        );
    }
}
