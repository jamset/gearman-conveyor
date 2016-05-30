<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 28.09.15
 * Time: 14:10
 */
namespace Gearman\Conveyor\ProcessManager;

use Gearman\Conveyor\ProcessManager\Abstracts\BaseProcessManager;
use React\ProcessManager\Pm as Manager;
use Gearman\Conveyor\Inventory\GearmanDto;

class ReactProcessManager extends BaseProcessManager
{
    /**
     * @var \GearmanJob
     */
    protected $job;

    /**
     * @var GearmanDto
     */
    protected $gearmanDto;

    public function manage()
    {
        $this->manager = new Manager();
        $this->manager->setProcessManagerDto($this->gearmanDto->getProcessManagerDto());

        //Gearman worker block any signal besides SIGKILL,
        //so its become impossible to correct send SIGTERM and handle it (execute some other actions before termination)
        $this->manager->setSigTermBlockingAgent(true);

        $this->manager->manage();

        $this->manager->getExecutionDto()->setExecutionMessage("PM with id " . $this->gearmanDto->getTaskId() . " going to finish.");
        $this->manager->getExecutionDto()->setTaskId($this->gearmanDto->getTaskId());

        $this->job->sendComplete(serialize($this->manager->getExecutionDto()));

        return null;
    }


}