<?php

namespace TalentInternational\MailWeb\Facades;

use Illuminate\Support\Facades\Facade;

class MailWeb extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MailWeb';
    }
}
