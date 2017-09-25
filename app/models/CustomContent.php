<?php

/**
 * Class CustomContent
 */
class CustomContent extends Content
{

    protected $fields;
    private $class_id = null;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customClass()
    {
        return $this->belongsTo('CustomClass', 'class_id', 'id');
    }


    public function createFromInput()
    {

        parent::createFromInput();
        $this->customClass()->associate(CustomClass::find(Route::current()->getParameter('classId')));
        $this->classId = $this->customClass()->id; //TODO check this

        $data = [];
        foreach ($this->customClass->customFields as $field) {
            if ($field->validation != '') {
                $this->setRule($field->name, $field->validation);
            }
            $data[$field->name] = $field->assignInputValue();
        }

        $this->content_data = json_encode($data);

        $this->content_text = $this->customClass->applyTemplate($data);


    }
}
