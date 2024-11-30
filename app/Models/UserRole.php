<?php

namespace App\Models;

use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRole
 *
 * @property int $user_role_id
 * @property string $role_name
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
class UserRole extends Model
{
    use ObservantTrait;

    protected $table = 'user_role';

    protected $casts = [
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $fillable = [
        'user_role_name',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
