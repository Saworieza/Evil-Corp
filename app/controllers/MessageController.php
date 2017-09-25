<?php

use custom\helpers\Responder;
use custom\exceptions\ApiException;

/**
 * Class MessageController
 */
class MessageController extends BaseController
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList()
    {
        if (Input::has('sent')) {
            $msg = UserMessage::where('from_id', Auth::user()->id)
                ->with('toUser')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $msg = UserMessage::where('to_id', Auth::user()->id)
                ->with('fromUser')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return Responder::json(true)->withDataTransform($msg, 'MessageTransformer')->send();
    }


    /**
     * @param $messageId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($messageId)
    {
        $msg = UserMessage::findOrFail($messageId);

        if ($msg->to_id != Auth::user()->id) {
            return Responder::json(false)->withMessage('access_denied')->send();
        }

        $msg->read = !$msg->read;
        $msg->save();

        return Responder::json(true)->withData((bool)$msg->read)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        $to = Input::get('to_id');

        $tot = 0;

        if (is_array($to)) {
            foreach ($to as $dest) {
                if (isset($dest['id'])) {
                    UserMessage::send(MSG_PRIVATE, $dest['id'], e( Input::get('body')) );
                    $tot++;
                }
            }
        } else {
            e('pep');
            UserMessage::send(MSG_PRIVATE, $to, e( Input::get('body') ));
            $tot++;

        }

        if ($tot > 0) {
            return Responder::json(true)->withMessage('message_sent')->withAlert('success')->send();
        } else {
            return Responder::json(true)->withMessage('no_message_sent')->send();

        }
    }

}