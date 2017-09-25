<?php

namespace custom\observers;

use custom\helpers\Responder;

class AttachObserver {

    public function deleting($model)
    {
        if($model->attachments)
        {
            foreach($model->attachments as $attach)
            {
                $attach->delete();
            }

        }
    }
}
