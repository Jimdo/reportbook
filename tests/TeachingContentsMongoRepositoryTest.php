<?php

namespace Jimdo\Reports;

use PHPUnit\Framework\TestCase;

class TeachingContentMongoRepository extends TestCase
{
    /**
     * @test
     */
    public function itShouldFindItemsByCombinedTextSearch()
    {
        $uri = 'mongodb://' . getenv('MONGO_IP') . ':27017';

        $teachingContentsRepository = new TeachingContentsMongoRepository(
            new \MongoDB\Client($uri)
        );

        $results = $teachingContentsRepository->search('datenbank');

        $this->assertCount(3, $results);

        $results = $teachingContentsRepository->search('umweltschutz');

        $this->assertCount(1, $results);

        $results = $teachingContentsRepository->search('datenbank umweltschutz');

        $this->assertCount(4, $results);
    }
}
