<?php

use custom\helpers\Responder;
use custom\exceptions\ApiException;

/**
 * Class EventController
 */
class EventController extends BaseController
{


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws custom\exceptions\ApiException
     */
    public function getList()
    {
        $events = Content::where('class_id', CONTENT_EVENT);

        if (Input::has('space_code')) {
            $spaceId = Space::getByCode(Input::get('space_code'))->id;

            if (Auth::user()->inSpace($spaceId) < ROLE_MEMBER) {
                throw new ApiException('access_denied');
            }

            $events = $events->where('space_id', $spaceId);

        } else {

            $spaces = Auth::user()->spaceMember->lists('id');
            $events = $events->whereIn('space_id', $spaces)->with('space');
        }

        if (Input::has('view')) {
            if (Input::get('view') == 'short') {
                $events = $events->whereHas(
                    'calendar', function ($q) {
                        $q->where('start_date', '>', \Carbon\Carbon::yesterday());
                    }
                );
            }

        }
        $events = $events->get();

        return Responder::json(true)->withDataTransform($events, 'EventTransformer')
            ->dataSort('start_date')
            ->dataTake(4)
            ->send();
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendar()
    {

        $spaces = Auth::user()->spaceMember->lists('id');

        if (count($spaces) == 0) {
            return;
        }

        $events = Content::where('class_id', CONTENT_EVENT)
            ->with('calendar')
            ->whereIn('space_id', $spaces)
            ->whereHas(
                'calendar', function ($q) {
                    $q->where('start_date', '>', date('Y-m-d H:i:s', strtotime("-90 days")));
                }
            )
            ->get()->toArray();


        $tasks = Task::myTasks()->where('state', '<', 3)->with('calendar')
            ->whereHas(
                'calendar', function ($q) {
                    $q->where('start_date', '>', date('Y-m-d H:i:s', strtotime("-90 days")));
                }
            )->get()->toArray();


        return Responder::json(true)
            ->withDataTransform($events, "CalendarEventTransformer", 'events')
            ->withDataTransform($tasks, "CalendarTaskTransformer", 'tasks')
            ->send();

    }

    public function update($id)
    {
        $content = Content::findOrFail($id);

        $cal = Calendar::where('calendarable_id', $id)->where('calendarable_type', 'Content')->first();

        if(empty($cal))
            throw new ApiException('not_found');

        $cal->start_date = Input::get('start_date');
        $cal->end_date   = Input::get('end_date');
        $cal->all_day    = Input::get('all_day');
        $cal->save();

        $data = $content->content_data;

        $data['start_date'] = Input::get('start_date');
        $data['end_date']   = Input::get('end_date');
        $data['all_day']    = Input::get('all_day');

        $content->content_data = json_encode($data);
        $content->save();

        $atts = DB::table('attendants')->where('calendar_id', $cal->id)->lists('user_id');

        $link = "<a href='".\URL::to('/')."/#/post/".$content->id."'>".$content->id."</a>";

        $body = trans('messages.event_changed',['link'=>$link]) ;


        foreach($atts as $user)
        {
            if($user != Auth::user()->id)
                \UserMessage::create(['to_id'=>$user, "body"=>$body]);

        }

        DB::table('attendants')->where('calendar_id', $cal->id)->delete();

        return Responder::json(true)->withData($content)->send();

    }

}