<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 21.09.15
 * Time: 0:33
 */
namespace Gearman\Conveyor\ProcessManager;

use Gearman\Conveyor\ProcessManager\Inventory\Exceptions\ProcessManagerException;
use Gearman\Conveyor\Inventory\GearmanParamsConstants;

class ProcessManagerFactory
{
    /**Return an object, that will manage gearman workers as separate processes,
     * based on load manager info.
     *
     * @param $managerType
     * @return ReactProcessManager|null
     * @throws ProcessManagerException
     */
    public static function getProcessManager($managerType)
    {
        $processManager = NULL;

        switch ($managerType) {
            case(GearmanParamsConstants::REACT_PROCESS_MANAGER):
                $processManager = new ReactProcessManager();
                break;
            default:
                throw new ProcessManagerException("Process manager wasn't set.");
        }

        return $processManager;
    }


}