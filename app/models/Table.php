<?php

/**
 * Class Table
 */
class Table extends Content
{

    protected $classId = 2;

    public function createFromInput()
    {
        parent::createFromInput();

        $this->setRule('table', 'required');

        $table = Input::get('table');
        for ($i = 0; $i < count($table['cols']); ++$i) {
            $isNum = true;
            for ($j = 0; $j < count($table['rows']); ++$j) {
                $isNum = $isNum && is_numeric($table['rows'][$j][$i]);
            }
            if ($isNum) {
                $table['cols'][$i]['type'] = 'num';
            }
        }

        $table['big'] = count($table['rows']) > 10;

        $this->content_data = json_encode($table);

    }
}
