<?php

/**
 * Class Space
 */
class Space extends BaseModel
{

    protected $table = 'spaces';

    function __construct()
    {
        parent::__construct();
        $this->setRule('title', 'required|unique:spaces');
        $this->setRule('access', 'in:PU,PR');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany('Tag');
    }

    /**
     * @param $code
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getByCode($code)
    {
        return self::where('code', $code)->first();
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folders()
    {
        return $this->hasMany('Folder');
    }

    /**
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany('User', 'space_user')
            ->where('state', USER_STATE_ACTIVE)
            ->withPivot('role', 'last_visit')
            ->orderBy('last_visit', 'DESC')->select('full_name', 'code');
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getOptionsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function classes()
    {
        return $this->hasMany('CustomClass');
    }


    /**
     * Determine if a content class is valid for this space.
     * return className or False
     *
     * @param null $classId
     * @return bool|string
     */
    public function validateClass($classId = null)
    {
        if (!$classId) {
            $classId = Route::current()->getParameter('classId');
        }

        $content_classes = ['Message', 'Link', 'Table', 'Chart', 'Poll', 'ContentEvent', 'Location', 'Wiki'];

        if ($classId < count($content_classes)) {
            return $content_classes[$classId];

        } else {
            if ($cl = $this->classes()->find($classId)) {
                // custom class is active
                if ($cl->status) {
                    return "customcontent";
                }
            }
        }

        return false;
    }

}
