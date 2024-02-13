<?php

namespace Tests\Unit\Models;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Status;
use App\Traits\HasLikes;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_comment_belongs_to_a_user()
    {
        $comment = factory(Comment::class)->create();

        $this->assertInstanceOf(User::class, $comment->user);
    }

    /** @test */
    function a_comment_belongs_to_a_status()
    {
        $comment = factory(Comment::class)->create();

        $this->assertInstanceOf(Status::class, $comment->status);
    }

    /** @test */
    function a_comment_model_must_use_the_trait_has_likes()
    {
        $this->assertClassUsesTrait(HasLikes::class, Comment::class);
    }

    /** @test */
    function a_comment_must_have_a_path()
    {
        $comment = factory(Comment::class)->create();
        $this->assertEquals(route('statuses.show', $comment->status_id) . '#comment-' . $comment->id, $comment->path());
        // statuses/1#comment-1
    }
}
