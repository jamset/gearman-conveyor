<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 31.10.15
 * Time: 23:52
 */
namespace Gearman\Conveyor\Adapters\Abstracts;

use Gearman\Conveyor\Adapters\Interfaces\Adapted;
use React\ProcessManager\Inventory\TerminatorPauseStanderConstants;
use React\ProcessManager\Inventory\TerminatorPauseStanderDto;
use React\ProcessManager\TerminatorPauseStander;
use React\PublisherPulsar\Inventory\PerformerConstants;
use React\PublisherPulsar\Inventory\PerformerDto;
use React\PublisherPulsar\Performer;
use TasksInspector\Inventory\ExecutionDto;
use Monolog\Logger;

abstract class BaseAdapted
{
    /**
     * @var Performer
     */
    protected $zmqPerformer;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var bool
     */
    protected $scriptWasInterrupted;

    /**
     * @var TerminatorPauseStander
     */
    protected $terminatorPauseStander;

    /**
     * @var ExecutionDto
     */
    protected $executionDto;

    /**
     * @var int
     */
    protected $taskId;

    /**
     * @var string
     */
    protected $errorMessage = '';

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var mixed
     */
    protected $params;

    /**
     * @var bool
     */
    protected $errorForTasksInspectorExist = false;

    /**
     * @return null
     * @throws \React\ProcessManager\Inventory\Exceptions\ProcessManagerException
     */
    protected function checkPmCommand()
    {
        $this->terminatorPauseStander->checkSubscription();
        $this->terminatorPauseStander->standOnPauseIfMust();

        return null;
    }

    /**
     * @return TerminatorPauseStander
     */
    public function getTerminatorPauseStander()
    {
        return $this->terminatorPauseStander;
    }

    /**
     * @param TerminatorPauseStander $terminatorPauseStander
     */
    public function setTerminatorPauseStander($terminatorPauseStander)
    {
        $this->terminatorPauseStander = $terminatorPauseStander;
    }

    /**
     * @return boolean
     */
    public function isErrorForTasksInspectorExist()
    {
        return $this->errorForTasksInspectorExist;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return int
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return Performer
     */
    public function getZmqPerformer()
    {
        return $this->zmqPerformer;
    }

    /**
     * @param Performer $zmqPerformer
     */
    public function setZmqPerformer($zmqPerformer)
    {
        $this->zmqPerformer = $zmqPerformer;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param string $moduleName
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return boolean
     */
    public function isScriptWasInterrupted()
    {
        return $this->scriptWasInterrupted;
    }

    /**
     * @param boolean $scriptWasInterrupted
     */
    public function setScriptWasInterrupted($scriptWasInterrupted)
    {
        $this->scriptWasInterrupted = $scriptWasInterrupted;
    }


    /**
     * @return ExecutionDto
     */
    public function getExecutionDto()
    {
        return $this->executionDto;
    }

    /**
     * @param ExecutionDto $executionDto
     */
    public function setExecutionDto($executionDto)
    {
        $this->executionDto = $executionDto;
    }

    public function initCommunicators()
    {
        $performerDto = new PerformerDto();
        $performerDto->setLogger($this->logger);
        $performerDto->setModuleName(PerformerConstants::PERFORMER . $this->moduleName);
        $this->zmqPerformer = new Performer($performerDto);

        $terminatorPauseStanderDto = new TerminatorPauseStanderDto();
        $terminatorPauseStanderDto->setLogger($this->logger);
        $terminatorPauseStanderDto->setModuleName(TerminatorPauseStanderConstants::TERMINATOR_PAUSE_STANDER . $this->moduleName);
        $this->terminatorPauseStander = new TerminatorPauseStander($terminatorPauseStanderDto);
        $this->executionDto = new ExecutionDto();

        return null;
    }


}
