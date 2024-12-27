<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RolePermission
 *
 * @property int $permission_id
 * @property int $user_role_id
 * @property int $menu_id
 * @property int|null $permission_type
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
class RolePermission extends Model
{
    use ObservantTrait;

    protected $table = 'role_permission';

    protected $primaryKey = 'permission_id';

    protected $casts = [
        'user_role_id' => 'int',
        'menu_id' => 'int',
        'permission_type' => 'int',
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $fillable = [
        'user_role_id',
        'menu_id',
        'permission_type',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
