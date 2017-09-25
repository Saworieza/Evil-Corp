<?php

namespace custom\observers;

use custom\helpers\Responder;

class CommentObserver {

    public function deleting($model)
    {
        if($model->comments)
        {
            foreach($model->comments as $comment)
            {
                $comment->delete();
            }
        }
    }
}
