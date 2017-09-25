<?php

use custom\interfaces\Calendarable;
use custom\interfaces\Attachable;
use custom\interfaces\Commentable;
use custom\interfaces\Authorizable;
use custom\exceptions\ApiException;

/**
 * Class Task
 */
class Task extends BaseModel implements Calendarable, Attachable, Commentable, Authorizable
{

    protected $table = 'tasks';
    protected $fillable = ['user_id', 'title', 'description', 'content_id', 'state'];

    /**
     * @param $field
     * @param $value
     * @param $taskRole
     * @return bool
     * @throws custom\exceptions\ApiException
     */
    public function updateField($field, $value, $taskRole)
    {
        $fieldSet = [
            'title'       => TASK_ROLE_CREATOR,
            'description' => TASK_ROLE_CREATOR,
            'state'       => TASK_ROLE_ASSIGNED,
            'priority'    => TASK_ROLE_CREATOR
        ];

        if (array_key_exists($field, $fieldSet)) {
            if ($taskRole >= $fieldSet[$field]) {
                $this->$field = $value;
                Input::replace([$field => $value]);
                if (!$this->withValidation($field)->save()) {
                    throw new ApiException($this->validator);
                }

                return true;
            } else {
                throw new ApiException('access_denied');

            }

        }

        return false;


    }

    /**
     * @param null $fieldName
     * @return $this
     */
    public function withValidation($fieldName = null)
    {
        $rules = [
            'title'    => 'required|min:3',
            'state'    => 'in:0,1,2,3,4',
            'priority' => 'in:0,1,2,3'
        ];

        if (isset($fieldName)) {
            if (isset($rules[$fieldName])) {
                $this->setRule($fieldName, $rules[$fieldName]);
            }
        } else {
            foreach ($rules as $k => $v) {
                $this->setRule($k, $v);
            }
        }

        return $this;
    }


    public static function boot()
    {
        parent::boot();

        self::observe(new custom\observers\CommentObserver);
        self::observe(new custom\observers\AttachObserver);
    }


    /**
     * @return $this
     */
    public static function myTasks()
    {
        $tasks = static::with('assignedTo')->with('user')->where(
            function ($q) {
                $q->where('user_id', Auth::user()->id)
                    ->orWhereHas(
                        'assignedTo', function ($q) {
                            $q->where('user_id', Auth::user()->id);
                        }
                    );
            }
        );

        return $tasks;
    }

    /**
     * @param $query
     * @return mixed
     */
    public static function scopeComplete($query)
    {
        return $query
            ->with(
                [
                    'comments' => function ($query) {
                            $query->orderBy('id')
                                ->with('user')
                                ->with('likes')
                                ->with('likes.user')
                                ->with('attachments');
                        }
                ]
            )
            ->with('space')
            ->with('attachments')
            ->with('calendar');
    }

    /**
     * @param $interface
     * @return bool
     */
    public function authorize($interface)
    {
        if (in_array($interface, ['Commentable'])) {
            return true;
//            return Auth::User()->inSpace($this->space_id) >= ROLE_MEMBER ;
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany('Attachment', 'attachable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany('Comment', 'commentable');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function space()
    {
        return $this->belongsTo('Space');
    }

    /**
     * @param $dueDate
     */
    public function syncCalendar($dueDate)
    {

        if (isset($dueDate) && $dueDate != '') {

            $calendar = Calendar::where('calendarable_id', $this->id)->where('calendarable_type', 'Task')->first();
            if ($calendar) {
                $calendar->update(['start_date' => $dueDate, 'end_date' => $dueDate]);

            } else {

                $calendar = Calendar::create(
                    [
                        'calendarable_id'   => $this->id,
                        'calendarable_type' => 'Task',
                        'all_day'           => true,
                        'start_date'        => $dueDate,
                        'end_date'          => $dueDate
                    ]
                );
            }


        } else {

            if ($this->calendar) {
                $this->calendar()->delete();
            }

        }
    }

    /**
     * @param $value
     */
    public function assignSync($value)
    {
        $asg = $this->filterAssignments($value);
        $this->assignedTo()->sync($asg);
    }

    /**
     * @param $assigned
     * @return array
     */
    public function filterAssignments($assigned)
    {
        $users = array_filter(
            array_map(
                function ($i) {
                    if (isset($i['id'])) {
                        return $i['id'];
                    }
                }, $assigned
            )
        );

        return $users;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content()
    {
        return $this->belongsTo('Content');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function calendar()
    {
        return $this->morphOne('Calendar', 'calendarable');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo('User')->select('id', 'full_name');
    }

    /**
     * @return mixed
     */
    public function assignedTo()
    {
        return $this->belongsToMany('User', 'task_users', 'task_id', 'user_id')->select('user_id', 'full_name');
    }


}