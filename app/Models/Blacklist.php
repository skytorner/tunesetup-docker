<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Advertiser;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;

class Blacklist extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::saved(function ($blacklist) {
            $user       = Auth::user();
            $userTosave = User::find($user->id);

            $actions    = [
                'date'    => date("Y-m-d H:i:s"),
                'subject' => 'BL',
                'id'      => $blacklist->id,
            ];
            $existingActions = (null !== $userTosave->actions) ? $userTosave->actions : [];
            array_push($existingActions, $actions);
            $userTosave->actions = $existingActions;
            $userTosave->save();
        });
    }

    /**
     * get all Campaigns Id
     * 
     * @return  \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function campaignsId(): Attribute
    {
        return new Attribute(
            function($value) { return explode(",", $value); },
            function($value) { return implode(",", $value); }
        );
    }

    public function advertiser()
    {
        return $this->hasOne(Advertiser::class, 'adv_id', 'adv_id');
    }
}
