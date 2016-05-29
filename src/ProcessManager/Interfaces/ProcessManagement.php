<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 21.09.15
 * Time: 1:47
 */
namespace Gearman\Conveyor\ProcessManager\Interfaces;

use TasksInspector\Inventory\ExecutionDto;
use Gearman\Conveyor\Inventory\GearmanDto;

interface ProcessManagement
{
    /**
     * @return mixed
     */
    public function manage();

    /**
     * @return bool
     */
    public function isAllTasksCreated();

    /**
     * @return ExecutionDto
     */
    public function getExecutionDto();

    /**
     * @return ExecutionDto
     */
    public function setExecutionDto(ExecutionDto $executionDto);

    /**
     * @return GearmanDto
     */
    public function getGearmanDto();

    /**
     * @return \GearmanJob
     */
    public function getJob();

}