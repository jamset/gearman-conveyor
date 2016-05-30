<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 31.10.15
 * Time: 23:45
 */
namespace Gearman\Conveyor\Workers;

use AdjutantHandlers\Processes\Interfaces\SignalsExecutor;
use Gearman\Conveyor\Adapters\Abstracts\BaseAdapted;
use Gearman\Conveyor\Adapters\Abstracts\BaseAdapter;
use Gearman\Conveyor\Workers\Abstracts\BaseCommonWorker;
use TasksInspector\InspectionHelper;

class CommonWorker extends BaseCommonWorker implements SignalsExecutor
{
    /**
     * @var BaseAdapter
     */
    protected $adapter;

    /**
     * @var bool
     */
    protected $alreadyShutDown = false;

    /**
     * @var string
     */
    protected $shutDownType = "TypicalWorker";

    /**Allow to notify different modules about termination. Division in conditions is important.
     * @return null
     */
    public function shutDown()
    {
        //To protect call methods from null if it's empty worker
        if ($this->taskWasGot) {

            $this->logStartShutDown();

            if ($this->alreadyShutDown === false) { //for case of use PCNTL signals

                $this->logInitShutDown();

                $this->alreadyShutDown = true;

                /**
                 * @var BaseAdapted $fetchService
                 */
                $adaptedService = $this->adapter->getAdaptedService();

                if ($adaptedService->isScriptWasInterrupted() === true) {
                    if ($adaptedService->getZmqPerformer()->getPerformerEarlyTerminated()->isStandOnSubscription()) {
                        $adaptedService->getZmqPerformer()->pushPerformerEarlyTerminated();
                    }
                }

                if ($this->adapter->isJobInfoWasSent() === false) {

                    $errorMsg = "Worker process was terminated before task with id " .
                        $adaptedService->getExecutionDto()->getTaskId()
                        . " and params " . serialize($adaptedService->getParams()) . " was complete.";

                    $this->adapter->getJob()->sendComplete(serialize(InspectionHelper::prepareErrorExecutionDto(
                        $adaptedService->getExecutionDto()->getTaskId(),
                        $errorMsg
                    )));

                }
            }

            $this->logFinishShutDown();
        }

        return null;
    }

    /**
     * @return BaseAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param BaseAdapter $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }


}