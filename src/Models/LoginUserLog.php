<?php

namespace SimpleSoft\XePlugin\Loginlog\Models;

use Xpressengine\Database\Eloquent\DynamicModel;

use Xpressengine\Storage\File;
use Xpressengine\User\Models\User;

class LoginUserLog extends DynamicModel
{
    protected $table = 'loginlog';

    protected $casts = [
        'logined_at' => 'datetime',
    ];

    public $timestamps = false;

    public $incrementing = true;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
