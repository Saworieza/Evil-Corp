<?php

use custom\helpers\Midrepo;
use custom\helpers\Responder;
use custom\helpers\Notificator;

/**
 * Class CommentController
 */
class CommentController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        $commentable = Midrepo::getOrFail('object');

        $comments = $commentable->comments->load('user')->load('attachments')->load('likes.user');


        return Responder::json(true)->withData($comments)->send();
    }

    /**
     * @param $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->user_id != Auth::user()->id) {
            return Response::json(['success' => false]);
        }

        $comment->delete();

        return Responder::json(true)->send();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        if (!Input::has('body')) {
            return Response::json(['succes' => false]);
        }

        $commentable = Midrepo::getOrFail('object');

        if ((get_class($commentable) == 'Content')) {
            $commentable->updated_by = Auth::user()->id;
            $commentable->save();
        } else {
            $commentable->touch();
        }

        $newComment = $commentable->comments()->create(
            [
                'user_id' => Auth::user()->id,
                'body'    => nl2br(e(Input::get('body')))
            ]
        );

        Attachment::processInput('Comment', $newComment->id);

        Notificator::comment($newComment);

        $newComment->load('user')->load('attachments')->load('likes');
        return Responder::json(true)->withData($newComment)->send();
    }

}
