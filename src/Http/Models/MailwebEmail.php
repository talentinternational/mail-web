<?php

namespace TalentInternational\MailWeb\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MailwebEmail extends Model
{
    protected $guarded = ['id'];

    protected $appends = [
        'body',
        'from_email',
        'to_emails',
        'subject',
        'attachments',
    ];

    protected $dates = [
        'created_at'
    ];

    protected static function booted()
    {
        static::addGlobalScope('symfony', function (Builder $builder) {
            $builder->where('email', 'NOT LIKE', 'O:13:"Swift_Message"%');
        });
    }

    public function getEmailAttribute($value)
    {
        return unserialize($value);
    }

    public function getBodyAttribute()
    {
        return $this->email->getBody()->getParts()[1]->getBody();
    }

    public function getFromEmailAttribute()
    {
        return $this->email->getFrom()[0]->getAddress();
    }

    public function getToEmailsAttribute()
    {
        $to = [];
        foreach ($this->email->getTo() as $email) {
            $to[] = $email->getAddress();
        }
        return $to;
    }

    public function getSubjectAttribute()
    {
        return $this->email->getSubject();
    }

    public function getAttachmentsAttribute()
    {
        $attachments = [];
        $emailAttachments = $this->email->getAttachments();
        foreach ($emailAttachments as $attachment) {
            $attachments[] = [
                'name' => $attachment->getFilename(),
                // 'content' => $attachment->getBody()
            ];
        }
        return $attachments;
    }

    public function scopeFilterByDates($query, $start, $end)
    {
        return $query->when($start, function ($query, $start) {
            return $query->whereDate('created_at', '>=', $start);
        })->when($end, function ($query, $end) {
            return $query->whereDate('created_at', '<=', $end);
        });
    }
}
