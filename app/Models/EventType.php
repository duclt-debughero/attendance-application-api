<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EventType
 *
 * @property int $event_type_id
 * @property string $type_name
 * @property string $description
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
class EventType extends Model
{
    use ObservantTrait;

    protected $table = 'event_type';

    protected $primaryKey = 'event_type_id';

    public $timestamps = true;

    protected $casts = [
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $fillable = [
        'type_name',
        'description',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
