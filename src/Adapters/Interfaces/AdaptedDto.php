<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 06.11.15
 * Time: 0:47
 */
namespace Gearman\Conveyor\Adapters\Interfaces;

interface AdaptedDto
{
    /**
     * @return int | string
     */
    public function getTaskId();


}