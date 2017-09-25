<?php

/**
 * Class BaseModel
 */
class BaseModel extends Eloquent
{

    public $validator;
    public $errors;
    protected $guarded = ['id'];
    protected $rules = [];

    public static function boot()
    {
        parent::boot();

        static::saving(
            function ($model) {
                return $model->validate();
            }
        );
    }

    /**
     * @return int
     */
    public function getErrorsCount()
    {
        return count($this->errors);
    }

    /**
     * @param $field
     * @param callable $formatFunc
     */
    public function setFromInput($field, Closure $formatFunc = null)
    {
        if (Input::has($field)) {
            $val = Input::get($field);
            if ($formatFunc) {
                $val = $formatFunc($val);
            }
            $this->$field = $val;
        }
    }

    /**
     * @param $field
     * @param $rule
     */
    protected function setRule($field, $rule)
    {
        $this->rules[$field] = $rule;
    }

    /**
     * @return bool
     */
    protected function validate()
    {

        if ($this->exists) {
            $this->checkRulesForUpdate();
        }

        $this->validator = Validator::make(Input::all(), $this->rules);

        $niceNames = [];
        foreach (array_keys($this->rules) as $key) {
            $niceNames[$key] = $this->transFieldName($key);
        }

        $this->validator->setAttributeNames($niceNames);

        if ($this->validator->fails()) {
            $this->errors = $this->validator->messages()->all();
            return false;
        }

        return true;

    }

    /**
     *
     */
    protected function checkRulesForUpdate()
    {
        $searchFor = 'unique:' . $this->table;
        $searchReq = 'required';
        foreach ($this->rules as $field => $ruleSet) {
            $pos = strpos($ruleSet, $searchFor);
            if ($pos !== false) {
                $replace             = "unique:" . $this->table . "," . $field . "," . $this->id;
                $ruleSet             = str_replace($searchFor, $replace, $ruleSet);
                $this->rules[$field] = $ruleSet;
            }

            $pos = strpos($ruleSet, $searchReq);
            if ($pos !== false) {
                if (isset($this->$field)) {
                    $ruleSet             = str_replace('required', '', $ruleSet);
                    $this->rules[$field] = $ruleSet;

                }
            }
        }
    }

    /**
     * @param $key
     * @return mixed|string
     */
    private function transFieldName($key)
    {
        $key = str_replace('.', '_', $key);

        $name = Lang::get('fieldnames.' . $this->table . '_' . $key);

        if ($name == 'fieldnames.' . $this->table . '_' . $key) {
            return $key;
        }

        return $name;
    }
}

