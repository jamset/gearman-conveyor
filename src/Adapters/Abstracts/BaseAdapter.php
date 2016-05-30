<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 31.10.15
 * Time: 23:46
 */
namespace Gearman\Conveyor\Adapters\Abstracts;

use Gearman\Conveyor\Adapters\Interfaces\Adapted;
use Gearman\Conveyor\Adapters\Interfaces\AdaptedDto;
use Gearman\Conveyor\Adapters\Interfaces\Adapter;
use React\FractalBasic\Inventory\ErrorsConstants;
use React\ProcessManager\Inventory\DataTransferConstants;
use React\ProcessManager\Inventory\PmErrorConstants;
use React\PublisherPulsar\Inventory\PerformerSocketsParamsDto;
use TasksInspector\InspectionHelper;
use Monolog\Logger;
use React\EventLoop\Factory;
use React\EventLoop\Timer\Timer;
use React\Stream\Stream;

abstract class BaseAdapter implements Adapter
{
    /**
     * @var \GearmanJob
     */
    protected $job;

    /**
     * @var bool
     */
    protected $jobInfoWasSent = false;

    /**
     * @var Adapted
     */
    protected $adaptedService;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Stream
     */
    protected $read;

    /**
     * @var Stream
     */
    protected $write;

    /**
     * @var mixed
     */
    protected $loop;

    /**
     * @var array
     */
    protected $pmWorkerDta;

    /**
     * @var int
     */
    protected $maxTimerIteration = 5;

    public function __construct(BaseAdapted $adaptedService)
    {
        $this->adaptedService = $adaptedService;
        //$this->adaptedService->setExecutionDto(new ExecutionDto());

        return null;
    }

    public function initAdapted(\GearmanJob $job)
    {
        $this->loop = Factory::create();
        $this->read = new \React\Stream\Stream(STDIN, $this->loop);
        $this->read->bufferSize = 8192;
        $this->write = new \React\Stream\Stream(STDOUT, $this->loop);
        $this->write->bufferSize = 8192;

        $this->job = $job;

        //protect from repeated execution
        $initStart = false;
        $pmErrorDtoAlreadySent = false;

        /**
         * Receive sockets params json from PM to set it into performer
         */
        $this->read->on('data', function ($data) use ($initStart, $pmErrorDtoAlreadySent) {

            if (!is_array($this->pmWorkerDta)) {

                $this->pmWorkerDta = @json_decode($data, true);

                if (($this->pmWorkerDta !== false) && (is_array($this->pmWorkerDta))) {

                    if ($initStart === false) {
                        $initStart = true;

                        try {

                            $this->initBasicParams();

                            $this->adaptedService->getTerminatorPauseStander()
                                ->setPublisherPmSocketAddress($this->pmWorkerDta[DataTransferConstants::PUBLISHER_PM]);
                            $this->adaptedService->getTerminatorPauseStander()->setUSleepTime(5000000);

                            $performerSocketParams = new PerformerSocketsParamsDto();
                            $performerSocketParams->setRequestPulsarRsSocketAddress(
                                $this->pmWorkerDta[DataTransferConstants::REQUEST_PULSAR_RS]);
                            $performerSocketParams->setPublisherPulsarSocketAddress(
                                $this->pmWorkerDta[DataTransferConstants::PUBLISHER_PULSAR]);
                            $performerSocketParams->setPushPulsarSocketAddress(
                                $this->pmWorkerDta[DataTransferConstants::PUSH_PULSAR]);

                            $this->adaptedService->getZmqPerformer()->setSocketsParams($performerSocketParams);
                            $this->adaptedService->getZmqPerformer()->setLogger($this->logger);

                            $this->adaptedService->serviceExec();

                            $this->adaptedService->getExecutionDto()->setExecutionMessage($this->adaptedService->getParams());
                            $this->job->sendComplete(serialize($this->adaptedService->getExecutionDto()));

                            $this->jobInfoWasSent = true;
                            $this->logger->critical("Job complete was sent.");

                        } catch (\Exception $e) {

                            $errorMsg = "Adapter die in Exception with \$e: " . $e->getMessage() . "|params: "
                                . serialize($this->adaptedService->getParams());
                            //. $e->getTraceAsString();

                            $this->logger->critical($errorMsg . " | " . serialize($this->pmWorkerDta));

                            $this->job->sendComplete(serialize(InspectionHelper::prepareErrorExecutionDto(
                                $this->adaptedService->getTaskId(),
                                $errorMsg
                            )));

                            $this->jobInfoWasSent = true;
                            $this->logger->critical("Job complete with exception was sent.");

                            die();

                        }

                        $this->loop->nextTick(function () {
                            $this->loop->stop();
                        });
                    }

                } else {

                    if ($pmErrorDtoAlreadySent === false) {

                        $pmErrorDtoAlreadySent = true;

                        $pmErrorArr = [];

                        $pmErrorArr[DataTransferConstants::ERROR_LEVEL] = ErrorsConstants::CRITICAL;
                        $pmErrorArr[DataTransferConstants::ERROR_REASON] = PmErrorConstants::WORKER_NOT_RECEIVE_CORRECT_DTO;
                        $pmErrorArr[DataTransferConstants::ERROR_ELEMENT] = $this->pmWorkerDta;

                        //write to PM's allotted STDIN about critical error
                        $this->write->write(json_encode($pmErrorArr));

                        $this->loop->nextTick(function () {
                            $this->loop->stop();
                        });
                    }
                }
            }
        });

        $timerIteration = 0;

        $this->loop->addPeriodicTimer(3, function (Timer $timer) use (&$timerIteration) {

            if ($this->pmWorkerDta === null) {
                if ($timerIteration > $this->maxTimerIteration) {
                    $this->initBasicParams();
                    die();
                }
                $timerIteration++;
            } else {
                $timer->cancel();
            }
        });

        $this->loop->run();

        if ($pmErrorDtoAlreadySent) {
            die();
        }
    }

    /**
     * @return null
     */
    protected function initBasicParams()
    {
        $this->adaptedService->setParams(unserialize($this->job->workload()));

        if ($this->adaptedService->getParams() instanceof AdaptedDto) {
            $this->adaptedService->getExecutionDto()->setTaskId(
                $this->adaptedService->getParams()->getTaskId()
            );
        } else {
            $params = $this->adaptedService->getParams();
            $this->adaptedService->getExecutionDto()->setTaskId($params['task_id']);
        }

        return null;
    }

    /**
     * @return BaseAdapted
     */
    public function getAdaptedService()
    {
        return $this->adaptedService;
    }

    /**
     * @param BaseAdapted $adaptedService
     */
    public function setAdaptedService($adaptedService)
    {
        $this->adaptedService = $adaptedService;
    }

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
    public function setJob($job)
    {
        $this->job = $job;
    }

    /**
     * @return boolean
     */
    public function isJobInfoWasSent()
    {
        return $this->jobInfoWasSent;
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
