<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Promoter extends User
{
    protected $attributes = [
        'role' => UserRoleEnum::PROMOTER->value,
    ];

    protected $with = [
        'credentials'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'registry',
        'role',
        'email_verified_at',
        'updated_at',
        'deleted_at',
        'credentials'
    ];

    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', UserRoleEnum::PROMOTER->value);
        });
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'promoter_id');
    }

    public function credentials()
    {
        return $this->hasOne(PaggueCredentials::class,'promoter_id','id');
    }

    #[Scope]
    protected function findPromoterByTicketsId(Builder $query, string $ticketsId): Promoter
    {
        return $query->where('users.id', function ($query) use ($ticketsId){
            $query->select('users.id')->from('users')
                ->join('events','users.id','=','events.promoter_id')
                ->join('tickets','events.id','=','tickets.event_id')
                ->where('tickets.id', $ticketsId)
                ->first();
            })
        ->first();
    }
}
