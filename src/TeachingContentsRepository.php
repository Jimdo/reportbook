<?php

namespace Jimdo\Reports;

interface TeachingContentsRepository
{
    /**
     * @param string $search
     * @return string[]
     */
    public function search(string $search): array;
}
