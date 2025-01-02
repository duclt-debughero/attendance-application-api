<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Libs\EncryptUtil;
use App\Traits\ObservantTrait;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class MstUser
 *
 * @property int $user_id
 * @property string|null $email_address
 * @property string|null $password
 * @property string|null $password_token
 * @property Carbon|null $password_token_expire
 * @property string|null $user_name
 * @property string|null $telephone_number
 * @property Carbon|null $last_login_time
 * @property int|null $user_role_id
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
class MstUser extends Authenticatable
{
    use ObservantTrait;

    protected $table = 'mst_user';

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $casts = [
        'password_token_expire' => 'datetime',
        'last_login_time' => 'datetime',
        'user_role_id' => 'int',
        'del_flg' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
        'deleted_by' => 'int',
    ];

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'email_address',
        'password',
        'password_token',
        'password_token_expire',
        'user_name',
        'telephone_number',
        'last_login_time',
        'user_role_id',
        'del_flg',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Decrypt user_name value before response
     * @param mixed $value
     */
    public function getUserNameAttribute($value) {
        return EncryptUtil::decryptAes256($value);
    }

    /**
     * Decrypt email_address value before response
     * @param mixed $value
     */
    public function getEmailAddressAttribute($value) {
        return EncryptUtil::decryptAes256($value);
    }

    /**
     * Encrypt user_name value when assigned
     * @param mixed $value
     */
    public function setUserNameAttribute($value) {
        $this->attributes['user_name'] = EncryptUtil::encryptAes256($value);
    }

    /**
     * Encrypt email_address value when assigned
     * @param mixed $value
     */
    public function setEmailAddressAttribute($value) {
        $this->attributes['email_address'] = EncryptUtil::encryptAes256($value);
    }

    /**
     * Encrypt password value when assigned
     * @param mixed $value
     */
    public function setPasswordAttribute($value) {
        $this->attributes['password'] = EncryptUtil::encryptSha256($value);
    }
}
