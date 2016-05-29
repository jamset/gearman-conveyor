<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 30.09.15
 * Time: 2:32
 */
namespace Gearman\Conveyor\Inventory;

use React\ProcessManager\LoadManager\Inventory\LoadManagerDto;
use React\PublisherPulsar\Inventory\PublisherPulsarDto;

class GearmanDto
{
    /**
     * @var string
     */
    protected $managerType;

    /**
     * @var mixed
     */
    protected $processManagerDto;

    /**
     * @var int | string
     */
    protected $taskId;

    /**
     * @return int|string
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param int|string $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @return string
     */
    public function getManagerType()
    {
        return $this->managerType;
    }

    /**
     * @param string $managerType
     */
    public function setManagerType($managerType)
    {
        $this->managerType = $managerType;
    }

    /**
     * @return mixed
     */
    public function getProcessManagerDto()
    {
        return $this->processManagerDto;
    }

    /**
     * @param mixed $processManagerDto
     */
    public function setProcessManagerDto($processManagerDto)
    {
        $this->processManagerDto = $processManagerDto;
    }


}