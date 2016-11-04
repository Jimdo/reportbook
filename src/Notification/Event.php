<?php

namespace Jimdo\Reports\Notification;

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
