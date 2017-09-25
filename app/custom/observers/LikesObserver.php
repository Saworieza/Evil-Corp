<?php

namespace custom\observers;

class LikesObserver {

    public function deleting($model)
    {
        if($model->likes)
        {
            foreach($model->likes as $like)
            {
                $like->delete();
            }
        }
    }

}