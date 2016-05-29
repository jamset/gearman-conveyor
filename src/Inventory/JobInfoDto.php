<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 13.10.15
 * Time: 4:12
 */
namespace Gearman\Conveyor\Inventory;

class JobInfoDto
{
    /**
     * @var string
     */
    protected $info;

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo($info)
    {
        $this->info = $info;
    }


}