<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 21.09.15
 * Time: 1:49
 */
namespace Gearman\Conveyor\ProcessManager\Abstracts;

use Gearman\Conveyor\ProcessManager\Interfaces\ProcessManagement;
use TasksInspector\Inventory\ExecutionDto;
use Gearman\Conveyor\Inventory\GearmanDto;

abstract class BaseProcessManager implements ProcessManagement
{

    /**
     * @var \GearmanJob
     */
    protected $job;

    /**
     * @var GearmanDto
     */
    protected $gearmanDto;

    /**
     * @var mixed
     */
    protected $manager;

    /**
     * @return \GearmanJob
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param \GearmanJob $job
     */
    public function setJob(\GearmanJob $job)
    {
        $this->job = $job;
    }

    /**
     * @return GearmanDto
     */
    public function getGearmanDto()
    {
        return $this->gearmanDto;
    }

    /**
     * @param GearmanDto $gearmanDto
     */
    public function setGearmanDto($gearmanDto)
    {
        $this->gearmanDto = $gearmanDto;
    }

    public function isAllTasksCreated()
    {
        return $this->manager->isAllTasksCreated();
    }

    public function getExecutionDto()
    {
        return $this->manager->getExecutionDto();
    }

    public function setExecutionDto(ExecutionDto $executionDto)
    {
        $this->manager->setExecutionDto($executionDto);
        return $this;
    }


}
