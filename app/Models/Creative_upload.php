<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class Creative_upload extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(function ($creative) {
            $user       = Auth::user();
            $userTosave = User::find($user->id);

            $actions    = [
                'date'    => date("Y-m-d H:i:s"),
                'subject' => 'KIT',
                'id'      => $creative->id,
            ];
            $existingActions = (null !== $userTosave->actions) ? $userTosave->actions : [];
            array_push($existingActions, $actions);
            $userTosave->actions = $existingActions;
            $userTosave->save();
        });
    }
}
