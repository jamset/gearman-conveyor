<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 28.10.15
 * Time: 14:45
 */
namespace Gearman\Conveyor\Clients\Abstracts;

use App\FWIndependent\Clients\Abstracts\BaseClient;
use Gearman\Conveyor\Clients\Inventory\ClientConstants;
use TasksInspector\Inspector;
use TasksInspector\Inventory\ExecutionDto;
use Gearman\Conveyor\Inventory\GearmanErrorsConstants;
use Monolog\Logger;

abstract class BaseGearmanClient extends BaseClient
{
    /**
     * @var \GearmanClient
     */
    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new \GearmanClient();

        return null;
    }

    public function initCallbacks()
    {
        $this->client->setStatusCallback([$this, 'handleStatus']);
        $this->client->setDataCallback([$this, 'handleData']);
        $this->client->setCompleteCallback([$this, 'handleComplete']);
        $this->client->setFailCallback([$this, 'handleFail']);
        $this->client->setWarningCallback([$this, 'handleWarning']);

        return null;
    }

    protected function handleTasks()
    {
        $this->client->runTasks();

        $this->logger->debug("Client finished.");
        $this->logger->debug("Start tasks inspection.");

        $this->inspectionDto = $this->tasksInspector->inspect();

        $this->logger->debug($this->inspectionDto->getInspectionMessage());
        $this->logger->notice("Inspection message: " . serialize($this->inspectionDto->getInspectionMessage()));

        $this->handleInspection();

        return null;
    }

    public function handleComplete(\GearmanTask $task)
    {
        /**
         * @var ExecutionDto $taskData
         */
        $taskData = @unserialize($task->data());

        try {

            $this->tasksInspector->checkTaskDataType($taskData);

            if (!($taskData->isErrorExist())) {

                $this->tasksInspector->unsetCreatedTask($taskData->getTaskId());
                $this->tasksInspector->incrementCorrectlyExecuted();

            } else {

                if ($taskData->isCriticalError()) {

                    $this->logger->critical($taskData->getErrorMessage());
                    $this->tasksInspector->setExecutedWithError($taskData);
                    $this->tasksInspector->inspect();
                    die();

                } else {

                    $this->tasksInspector->setExecutedWithError($taskData);

                }

            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage() . $this->loggerPostfix);
        }

        $this->logger->notice("COMPLETE: " . $task->jobHandle() . ", " . serialize($taskData));
        return null;
    }

    public function handleWarning(\GearmanTask $task)
    {

    }

    public function handleFail(\GearmanTask $task)
    {
        //echo "FAIL: " . $task->jobHandle() . "\n";

        return null;
    }

    public function handleStatus(\GearmanTask $task)
    {
        /*echo "STATUS: " . $task->jobHandle() . " - " . $task->taskNumerator() .
            "/" . $task->taskDenominator() . "\n";*/

        return null;
    }

    public function handleData(\GearmanTask $task)
    {
        /*$taskData = $task->data();
        echo $taskData . "\n";

        $data = @unserialize($taskData);

        if ($data) {
            $this->logger->notice($data);
        } else {
            $this->logger->error(GearmanErrorsConstants::RECEIVED_NOT_SERIALIZED_DATA);
        }*/

        return null;
    }

}
