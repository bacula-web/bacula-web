<?php

namespace App\Entity;

use Core\Utils\CUtils;

class Job
{
    /**
     * @var string
     */
    private $level;

    /**
     * @var int
     */
    private $jobbytes;

    /**
     * @return string
     */
    public function getLevel(): string
    {
        $joblevels = [
            'D' => 'Differential',
            'I' => 'Incremental',
            'F' => 'Full',
            'V' => 'InitCatalog',
            'C' => 'Catalog',
            'O' => 'VolumeToCatalog',
            'd' => 'DiskToCatalog',
            'A' => 'Data'
        ];

        return $joblevels[$this->level];
    }

    /**
     * @return string
     */
    public function getJobBytes(): string
    {
        return CUtils::Get_Human_Size($this->jobbytes);
    }
}
