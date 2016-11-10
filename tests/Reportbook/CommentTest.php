<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveCommentConstruct()
    {
        $id = uniqid();
        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.12.2016';
        $content = 'Verbesserung 1';

        $comment = new Comment($id, $reportId, $userId, $date, $content);

        $this->assertEquals($reportId, $comment->reportId());
        $this->assertEquals($userId, $comment->userId());
        $this->assertEquals($date, $comment->date());
        $this->assertEquals($content, $comment->content());
        $this->assertEquals(Comment::STATUS_NEW, $comment->status());
        $this->assertInternalType('string', $comment->id());
    }

    /**
     * @test
     */
    public function itShouldHaveEditContent()
    {
        $id = uniqid();
        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.12.2016';
        $content = 'Verbesserung 1';

        $comment = new Comment($id, $reportId, $userId, $date, $content);

        $newContent = 'Neuer schöner Content';

        $comment->editContent($newContent);

        $this->assertEquals($newContent, $comment->content());
    }
    
    /**
     * @test
     */
    public function itShouldHaveStatusEditedAfterEditContent()
    {
        $id = uniqid();
        $reportId = uniqid();
        $userId = uniqid();
        $date = '20.12.2016';
        $content = 'Verbesserung 1';

        $comment = new Comment($id, $reportId, $userId, $date, $content);

        $newContent = 'Neuer schöner Content';

        $comment->editContent($newContent);

        $this->assertEquals(Comment::STATUS_EDITED, $comment->status());
    }
}
