<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 10.11.15
 * Time: 23:36
 */
namespace Gearman\Conveyor\Workers\Abstracts;

use Gearman\Conveyor\Workers\Inventory\WorkersConstants;
use Monolog\Logger;

abstract class BaseCommonWorker
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $shutDownType;

    /**
     * @var bool
     */
    protected $taskWasGot;

    public function work(\GearmanWorker $worker)
    {
        $worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);

        $getWorkAttempts = 0;

        do {

            $workRes = $worker->work();
            $getWorkAttempts++;

            $this->logger->debug("Get work attempt res: " . serialize($workRes));
            $this->logger->debug("Attempts number: " . serialize($getWorkAttempts));
            sleep(1);

            if ($workRes) {
                $this->taskWasGot = true;
            }

        } while ($workRes === false && $getWorkAttempts < WorkersConstants::MAX_GET_TASK_ATTEMPTS);

        if ($worker->returnCode() != GEARMAN_SUCCESS) {
            $this->logger->error("Not correct Gearman worker execution:" . $worker->returnCode());
        }

        return null;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function logStartShutDown()
    {
        $this->logger->alert("Come to shutDown in " . $this->shutDownType . "| PID: " . posix_getpid());

        return null;
    }

    public function logInitShutDown()
    {
        $this->logger->alert("Init shutDown work in " . $this->shutDownType . "| PID: " . posix_getpid());

        return null;
    }

    public function logFinishShutDown()
    {
        $this->logger->alert("Finish shutDown in " . $this->shutDownType . "| PID: " . posix_getpid());

        return null;
    }


}