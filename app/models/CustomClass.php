<?php

/**
 * Class CustomClass
 */
class CustomClass extends Eloquent
{

    protected $table = 'classes';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customFields()
    {
        return $this->hasMany('CustomField', 'class_id', 'id');
    }

    /**
     * @param $values
     * @return string
     */
    public function applyTemplate($values)
    {
        $template = $this->template == '' ? 'default' : $this->template;

        $res = View::make('templates.' . $template)
            ->with('fields', $this->customFields)
            ->with('values', $values)
            ->render();

        return $res;
    }

}