<?php

use custom\helpers\Responder;
use custom\helpers\Midrepo;
use custom\exceptions\ApiException;

/**
 * Class TaskController
 */
class TaskController extends BaseController
{

    /**
     * @param $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($taskId)
    {
        $task = Task::findOrFail($taskId);

        if ($task->user_id != Auth::user()->id) {
            return Responder::json(false)->send();

        }
        $task->delete();

        return Responder::json(true)->send();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function archive()
    {
        if (Midrepo::getOrFail('task_role') == TASK_ROLE_CREATOR) {
            $task           = Midrepo::getOrFail('task');
            $task->archived = !$task->archived;
            $task->save();

            return Responder::json(true)->withData($task->archived)->send();
        }

        return Responder::json(false)->send();

    }

    /**
     * @param $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOne($taskId)
    {
        $tasks = Task::myTasks()->where('id', $taskId)->Complete()->first();

        return Responder::json(true)->withDataTransform($tasks, 'TaskTransformer')->send();

    }

    /**
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getList()
    {

        $tasks = Task::myTasks();

        if (Input::has('content_id')) {
            $tasks = $tasks->where('content_id', Input::get('content_id'));
        }

        if (Input::has('task_id')) {

            $task = $tasks->where('id', Input::get('task_id'));
        }

        if (Input::has('space_code')) {
            $space = Space::getByCode(Input::get('space_code'));

            if (Auth::user()->inSpace($space->id) < ROLE_MEMBER) {
                return Responder::json(false)->withMessage('access_denied');
            }

            $tasks = $tasks->where('space_id', $space->id);
        }

        $tasks->where('archived', Input::get('archived', 0));

        $tasks->Complete();

        return Responder::json(true)->withDataTransform($tasks->get(), 'TaskTransformer')->send();

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCounters()
    {
        if (Input::has('contentId')) {
            $count = DB::table('tasks')
                ->where('archived', 0)
                ->where('content_id', Input::get('contentId'))
                ->where('state', '<', 3)->count();

            return Responder::json(true)->withData($count)->send();
        }

        $userId  = Auth::user()->id;
        $spaceId = null;
        if (Input::has('space_code')) {
            $spaceId = Space::getByCode(Input::get('space_code'))->id;
            if (Auth::user()->inSpace($spaceId) < ROLE_MEMBER) {
                return Responder::json(false)->send();
            }
        }

        $tot      = 0;
        $myTasks  = 0;
        $delTasks = 0;
        $tasks    = Task::myTasks()
            ->where('archived', 0)
            ->where('state', '<', 3);
        if (isset($spaceId)) {
            $tasks = $tasks->where('space_id', $spaceId);
        }

        $tasks = $tasks->get()->toArray();

        foreach ($tasks as $task) {
            $tot++;
            if ($task['user_id'] == $userId && count($task['assigned_to']) == 0) {
                $myTasks++;
            }

            if (count($task['assigned_to']) > 0 && findWhere($task['assigned_to'], ['user_id' => $userId])) {
                $myTasks++;
            }

            if ($task['user_id'] == $userId && count($task['assigned_to']) > 0) {
                $f = findWhere($task['assigned_to'], ['user_id' => $userId]);
                if (!$f) {
                    $delTasks++;
                }
                if ($f && count($task['assigned_to']) > 1) {
                    $delTasks++;
                }

            }

        }
        return Responder::json(true)->withData(['total' => $tot, 'my' => $myTasks, 'delegated' => $delTasks])->send();

    }

    /**
     * @param $taskId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($taskId)
    {
        $task     = Midrepo::getOrFail('task');
        $taskRole = Midrepo::getOrFail('task_role');

        if (!$field = Input::get('field', false)) {
            return Responder::json(false)->send();
        }

        $value = Input::get('value');

        if ($task->updateField($field, $value, $taskRole)) {
            return Responder::json(true)->send();
        }

        if ($field == 'due_date' && $taskRole == TASK_ROLE_CREATOR) {
            $task->syncCalendar($value);
            $task->load('assignedTo')->load('calendar');
            return Responder::json(true)->withDataTransform($task, 'TaskTransformer')->send();
        }

        if ($field == 'assigned_to' && $taskRole == TASK_ROLE_CREATOR) {
            $task->assignSync($value);
            $task->load('assignedTo')->load('calendar');
            return Responder::json(true)->withDataTransform($task, 'TaskTransformer')->send();
        }

        return Responder::json(false)->send();

    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws custom\exceptions\ApiException
     */
    public function store()
    {

        $task = new Task;

        $task->user_id     = Auth::user()->id;
        $task->description = Input::get('description', '');
        $task->title       = Input::get('title', '');
        $task->state       = Input::get('state', 0);
        $task->priority    = Input::get('priority', 1);
        $task->archived    = 0;
        $task->history     = json_encode(
            ['date' => date('Y-m-d H:i:s'), 'user' => Auth::user()->full_name, 'txt' => ' task created.']
        );
        if (Input::has('content_id')) {
            $content = Content::findOrFail(Input::get('content_id'));
            if (Auth::user()->inSpace($content->space_id) >= ROLE_MEMBER) {
                $task->content_id = $content->id;
                $task->space_id   = $content->space_id;
            }
        }

        if (Input::has('space_code')) {
            $spaceId = Space::getByCode(Input::get('space_code'))->id;
            if (Auth::user()->inSpace($spaceId) >= ROLE_MEMBER) {
                $task->space_id = $spaceId;
            } else {
                return Responder::json(false)->withMessage('access_denied')->send();
            }
        }

        if (!$task->withValidation()->save()) {
            throw new ApiException($task->validator);
        }


        if (Input::has('due_date')) {
            $task->syncCalendar(Input::get('due_date'));
        }

        if (Input::has('assigned_to')) {
            $task->assignSync(Input::get('assigned_to'));
        }

        Attachment::processInput('Task', $task->id);

        $task->load('user')->load('assignedTo')->load('attachments')->load('comments')->load('calendar');

        return Responder::json(true)->withDataTransform($task, 'TaskTransformer')->send();

    }

}