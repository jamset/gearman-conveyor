<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 21.09.15
 * Time: 0:54
 */
namespace Gearman\Conveyor\ProcessManager;

use Gearman\Conveyor\ProcessManager\Interfaces\ProcessManagement;
use Gearman\Conveyor\Inventory\GearmanDto;
use Monolog\Logger;

class ProcessManagerInitializer
{
    /**
     * @var \GearmanJob
     */
    protected $job;

    /**
     * @var ProcessManagement
     */
    protected $processManager;

    /**
     * @var GearmanDto
     */
    protected $gearmanDto;

    /**
     * @var Logger
     */
    protected $logger;

    public function initManager(\GearmanJob $job)
    {
        $this->job = $job;

        $this->gearmanDto = unserialize($job->workload());

        $this->processManager = ProcessManagerFactory::getProcessManager($this->gearmanDto->getManagerType());
        $this->processManager->setJob($job);
        $this->processManager->setGearmanDto($this->gearmanDto);

        try {
            $this->processManager->manage();
        } catch (\Exception $e) {
            $errorMessage = "Gearman process manager die with exception: " . $e->getMessage() . "| gearmanDto: "
                . serialize($this->gearmanDto);
            $this->logger->warning($errorMessage);
            $this->processManager->getExecutionDto()->setErrorMessage($errorMessage);
            die();
        }

        return null;
    }

    /**
     * @return \GearmanJob
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return ProcessManagement
     */
    public function getProcessManager()
    {
        return $this->processManager;
    }

    /**
     * @return GearmanDto
     */
    public function getGearmanDto()
    {
        return $this->gearmanDto;
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

}
