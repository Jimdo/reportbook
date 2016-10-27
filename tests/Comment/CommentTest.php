<?php

namespace Jimdo\Reports\Comment;

use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveCommentConstruct()
    {
        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.12.2016';
        $content = 'Verbesserung 1';

        $comment = new Comment($reportId, $userId, $date, $content);

        $this->assertEquals($reportId, $comment->reportId());
        $this->assertEquals($userId, $comment->userId());
        $this->assertEquals($date, $comment->date());
        $this->assertEquals($content, $comment->content());
    }

    /**
     * @test
     */
    public function itShouldHaveEditContent()
    {
        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.12.2016';
        $content = 'Verbesserung 1';

        $comment = new Comment($reportId, $userId, $date, $content);

        $newContent = 'Neuer schöner Content';

        $comment->editContent($newContent);

        $this->assertEquals($newContent, $comment->content());
    }


}
