<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 09.11.15
 * Time: 7:58
 */
namespace Gearman\Conveyor\Workers;

use App\FWIndependent\Adjutant\Processes\Interfaces\SignalsExecutor;
use Gearman\Conveyor\ProcessManager\ProcessManagerInitializer;
use Gearman\Conveyor\Workers\Abstracts\BaseCommonWorker;

class CommonPmWorker extends BaseCommonWorker implements SignalsExecutor
{
    /**
     * @var ProcessManagerInitializer
     */
    protected $processManagerInitializer;

    /**
     * @var bool
     */
    protected $alreadyShutDown = false;

    /**
     * @var string
     */
    protected $shutDownType = "PmWorker";

    /**Allow to send critical error to client if not all tasks completed and init die() into client.
     * @return null
     */
    public function shutDown()
    {
        //To protect call methods from null if it's empty worker
        if ($this->taskWasGot) {

            $this->logStartShutDown();

            if ($this->alreadyShutDown === false) {

                $this->alreadyShutDown = true;

                $manager = $this->processManagerInitializer->getProcessManager();

                if ($manager->isAllTasksCreated() === false) {

                    $this->logInitShutDown();

                    $manager->getExecutionDto()->setErrorExist(true);
                    $manager->getExecutionDto()->setCriticalError(true);
                    $manager->getExecutionDto()->setErrorMessage("Shutdown PM, but not all tasks was created. | "
                        . $manager->getExecutionDto()->getErrorMessage());

                    $manager->getExecutionDto()->setTaskId($manager->getGearmanDto()->getTaskId());

                    $manager->getJob()->sendComplete(serialize($manager->getExecutionDto()));
                }

            }

            $this->logFinishShutDown();
        }

        return null;
    }

    /**
     * @return ProcessManagerInitializer
     */
    public function getProcessManagerInitializer()
    {
        return $this->processManagerInitializer;
    }

    /**
     * @param ProcessManagerInitializer $processManagerInitializer
     */
    public function setProcessManagerInitializer($processManagerInitializer)
    {
        $this->processManagerInitializer = $processManagerInitializer;
    }

}
