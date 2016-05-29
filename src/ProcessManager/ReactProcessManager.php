<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 28.09.15
 * Time: 14:10
 */
namespace Gearman\Conveyor\ProcessManager;

use App\Console\Inventory\ConsoleConstants;
use Gearman\Conveyor\ProcessManager\Abstracts\BaseProcessManager;
use React\ProcessManager\LoadManager\Inventory\LoadManagerDto;
use React\ProcessManager\Inventory\ProcessManagerDto;
use React\ProcessManager\Pm as Manager;
use TasksInspector\Inventory\ExecutionDto;
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
        $this->manager->setSigTermBlockingAgent(true); //Gearman worker block any signal besides SIGKILL,
        // so its become impossible to correct send SIGTERM.
        $this->manager->manage();

        $this->manager->getExecutionDto()->setExecutionMessage("PM with id " . $this->gearmanDto->getTaskId() . " going to finish.");
        $this->manager->getExecutionDto()->setTaskId($this->gearmanDto->getTaskId());

        $this->job->sendComplete(serialize($this->manager->getExecutionDto()));

        return null;
    }


}