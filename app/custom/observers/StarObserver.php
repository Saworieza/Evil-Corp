<?php

namespace custom\observers;

class StarObserver {

    public function deleting($model)
    {
        if($model->stars)
        {
            foreach($model->stars as $star)
            {
                $star->delete();
            }
        }
    }
}