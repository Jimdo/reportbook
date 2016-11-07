<?php

namespace Jimdo\Reports\Notification\Events;

interface Event
{
    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return array
     */
    public function payload(): array;

}
