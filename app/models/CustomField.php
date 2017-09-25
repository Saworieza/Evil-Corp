<?php

/**
 * Class CustomField
 */
class CustomField extends Eloquent
{

    protected $table = 'class_fields';
    public $timestamps = false;


    /**
     * @return mixed
     */
    private function assign_string()
    {
        return Input::get($this->name, null);
    }

    /**
     * @return bool|mixed|string
     */
    private function assign_date()
    {
        $val = Input::get($this->name, null);
        if ($val == '@today') // TODO move this to getDefault
        {
            $val = Date('Y-m-d');
        }

        return $val;
    }

    /**
     * @return bool|mixed|string
     */
    private function assign_datetime()
    {
        $val = Input::get($this->name, null);
        if ($val == '@now') // TODO move this to getDefault
        {
            $val = Date('Y-m-d H:i:s');
        }

        return $val;
    }

    /**
     * @return float
     */
    private function assign_number()
    {
        return (float)Input::get($this->name, null);
    }

    /**
     * @return int
     */
    private function assign_checkbox()
    {
        return (int)Input::get($this->name, 0);
    }

    /**
     * @return array
     */
    private function assign_location()
    {
        $location = Input::get($this->name);

        $staticMapUrl = '<img src="http://maps.googleapis.com/maps/api/staticmap?center=%s,%s&zoom=11&size=200x200&sensor=false">';

        $staticMapUrl = sprintf($staticMapUrl, $location['lat'], $location['long']);

        return [
            'lat'  => (double)$location['lat'],
            'long' => (double)$location['long'],
            'img'  => $staticMapUrl
        ];
    }

    /**
     * @return float
     */
    private function assign_integer()
    {
        return (float)Input::get($this->name, null);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getDataOptionsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $token
     * @return mixed|null|string
     */
    private function tokenValue($token)
    {
        $delimiters = ['+', '.', '*', '/', '-', '(', ')'];
        if (in_array($token, $delimiters)) {
            return $token;
        }

        if ($token == '&') {
            return ' . ';
        }

        if (is_numeric($token)) {
            return $token;
        }

        if (Input::has($token)) {
            $val = Input::get($token, 0);
            if (is_string($val)) {
                $val = '"' . $val . '"';
            }
            return $val;
        }

        if (strpos($token, "'") >= 0 || strpos($token, '"') >= 0) {
            return $token;
        }

        return null;
    }

    /**
     * @return null
     */
    private function assign_calculated()
    {

        $formula = $this->data_options['formula'];
        //     if(strpos($formula,'$')>=0 || strpos($formula,'->')>=0 || strpos($formula,'::')>=0 ) return null;

        if ($formula == '') {
            return null;
        }

        $tokens = preg_split('/([\.\*\+\(\)\-\/\&])/m', $formula, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $formula = '';
        foreach ($tokens as $token) {
            $formula .= $this->tokenValue($token);
        }


        $val = null;

        eval("\$val = $formula;");

        return $val;
    }

    /**
     * @return mixed|null
     */
    private function assign_select()
    {
        if ($val = Input::get($this->name)) {
            if (in_array($val, $this->data_options['values'])) {
                return $val;
            }
        }
        return null;
    }

    /**
     * @return null
     */
    public function assignInputValue()
    {

        if (!in_array(
            $this->data_type,
            ['string', 'number', 'integer', 'text', 'date', 'datetime', 'select', 'calculated', 'checkbox', 'location']
        )
        ) {
            return null;
        }

        $method = 'assign_' . $this->data_type;

        return $this->$method();
    }

}