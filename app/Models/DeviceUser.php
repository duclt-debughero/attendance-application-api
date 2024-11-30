<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeviceUser
 *
 * @property int $device_id
 * @property int $user_id
 * @property string|null $device_token
 * @property string|null $access_token
 * @property Carbon|null $access_token_expire
 * @property string|null $refresh_token
 * @property Carbon|null $refresh_token_expire
 * @property int|null $del_flg
 * @property Carbon|null $created_at
 * @property int|null $created_by
 * @property Carbon|null $updated_at
 * @property int|null $updated_by
 * @property string|null $deleted_at
 * @property int|null $deleted_by
 *
 * @package App\Models
 */
class DeviceUser extends Model
{
    use ObservantTrait;

    public $timestamps = false;

    protected $table = 'device_user';

    protected $primaryKey = 'device_id';

    protected $casts = [
        'user_id' => 'int',
        'access_token_expire' => 'datetime',
        'refresh_token_expire' => 'datetime',
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $fillable = [
        'user_id',
        'device_token',
        'access_token',
        'access_token_expire',
        'refresh_token',
        'refresh_token_expire',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
