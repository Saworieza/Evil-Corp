<?php

/**
 * Class Mailer
 */
class Mailer extends Eloquent
{

    protected $table = 'mailer';
    protected $fillable = ['to', 'body'];


}