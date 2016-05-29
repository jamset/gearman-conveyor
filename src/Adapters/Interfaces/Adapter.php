<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 01.11.15
 * Time: 0:08
 */
namespace Gearman\Conveyor\Adapters\Interfaces;

interface Adapter
{
    /**
     * @return mixed
     */
    public function initAdapted(\GearmanJob $job);


}