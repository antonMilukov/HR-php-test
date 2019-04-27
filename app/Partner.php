<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    public static function getInstance()
    {
        return new self();
    }
}
