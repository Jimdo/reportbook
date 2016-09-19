<?php

namespace Jimdo\Reports;

class TeachingContentsMongoRepository implements TeachingContentsRepository
{
    /** @var MongoDB\Client */
    private $client;

    /**
     * @param MongoDB\Client $client
     */
    public function __construct(\MongoDB\Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $search
     * @return string[]
     */
    public function search(string $search): array
    {
        $result = $this->client->reportbook->teaching_contents->find(
            [
                '$text' => [ '$search' => $search ]
            ],
            [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array',
                    'array' => 'array'
                ]
            ]
        );

        return $result->toArray();
    }
}
