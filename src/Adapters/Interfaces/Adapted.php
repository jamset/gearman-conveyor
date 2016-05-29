<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 23.09.15
 * Time: 12:50
 */
namespace Gearman\Conveyor\Adapters\Interfaces;

interface Adapted
{
    /**
     * @param \GearmanJob $job
     * @return mixed
     */
    public function serviceExec();


}