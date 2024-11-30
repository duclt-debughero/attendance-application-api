<?php

namespace App\Models;

use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RoleMenu
 *
 * @property int $menu_id
 * @property string $menu_name
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
class RoleMenu extends Model
{
    use ObservantTrait;

    public $timestamps = false;

    protected $table = 'role_menu';

    protected $primaryKey = 'menu_id';

    protected $casts = [
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $fillable = [
        'menu_name',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
