<?php

namespace App\Repositories;

use App\Libs\ValueUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\{Auth, DB, Log};

abstract class BaseRepository
{
    protected $model;

    public function __construct() {
        $this->setModel();
    }

    abstract public function getModel();

    /**
     * Set model
     */
    public function setModel() {
        $this->model = app()->make($this->getModel());
    }

    /**
     * Find by id
     *
     * @param array|string|int $ids
     * @param bool $isFindAll
     * @return mixed|object|bool|null
     */
    public function findById($ids, $isFindAll = false) {
        try {
            $query = $this->model::query();
            $keys = $this->model->getKeyName();

            if (is_array($keys)) {
                foreach ($keys as $index => $key) {
                    $query->where($key, $ids[$index]);
                }
            } else {
                $query->where($keys, $ids);
            }

            if (! $isFindAll) {
                $query->whereValidDelFlg();
            }

            return $query->first();
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Update data
     *
     * @param int $id
     * @param array $params: params need match all fields of model
     * @param bool $isFindAll
     * @return object|mixed|boolean
     */
    public function update($id, $params, $isFindAll = false) {
        DB::beginTransaction();
        try {
            $query = $this->findById($id, $isFindAll);
            $query->fill($params);

            $result = $query->save($params);
            if ($result) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $query;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Create data
     *
     * @param array $params: params need match all fields of model
     * @return object|mixed|boolean
     */
    public function create($params) {
        DB::beginTransaction();
        try {
            $result = $this->model->create($params);
            if ($result) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Delete by id
     *
     * @param string|int $id
     * @return object|mixed|bool|null
     */
    public function deleteById($id) {
        DB::beginTransaction();
        try {
            $params = [
                'del_flg' => ValueUtil::constToValue('common.del_flg.INVALID'),
            ];
            $query = $this->findById($id);
            $query->fill($params);

            $result = $query->save($params);
            if ($result) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $query;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Save many
     *
     * @param array|\Illuminate\Database\Eloquent\Model[] $collections
     * @return array|\Illuminate\Database\Eloquent\Model[]|bool
     */
    public function saveMany($collections) {
        try {
            DB::transaction(function () use ($collections) {
                foreach ($collections as $key => $collection) {
                    if (! $collection->save()) {
                        return false;
                    }
                }
            });

            return $collections;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Update many record
     *
     * @param array $params
     * @param bool $isFindAll
     * @return \Illuminate\Support\Collection|bool
     */
    public function updateMany($params, $isFindAll = false) {
        try {
            $result = collect();
            DB::transaction(function () use ($params, $isFindAll, &$result) {
                foreach ($params as $key => $items) {
                    $query = $this->findById($params[$key][$this->model->getKeyName()], $isFindAll);
                    $query->fill($items);
                    if (! $query->save($items)) {
                        return false;
                    }
                    $result->push($query);
                }
            });

            return $result;
        } catch (Exception $e) {
            Log::error($e);

            return false;
        }
    }

    /**
     * Create many record
     *
     * @param array $params
     * @return bool
     */
    public function createMany($params) {
        DB::beginTransaction();
        try {
            $now = Carbon::now();
            $userLoginId = Auth::id();

            foreach ($params as $key => $items) {
                $params[$key]['created_at'] = $now;
                $params[$key]['created_by'] = $userLoginId;
                $params[$key]['updated_at'] = $now;
                $params[$key]['updated_by'] = $userLoginId;
            }

            $result = $this->model->insert($params);
            if ($result) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Perform an upsert operation on the model.
     *
     * @param array $data The data to be inserted or updated.
     * @param string|array|null $uniqueBy The unique identifier(s) to look for. Defaults to the model's primary key.
     * @param array|null $update The fields that can be updated. Defaults to the model's fillable fields.
     * @param array $exceptUpdate The fields that cannot be updated.
     *
     * @return mixed The result of the upsert operation.
     */
    public function upsert($data, $uniqueBy = null, $update = null, $exceptUpdate = []) {
        DB::beginTransaction();
        try {
            $this->model->timestamps = true;

            // Remove $exceptUpdate from $update
            $update ??= $this->model->getFillable();
            $update = array_diff($update, $exceptUpdate);

            $uniqueBy ??= $this->model->getKeyName();
            if (! is_array($uniqueBy)) {
                $uniqueBy = [$uniqueBy];
            }

            $result = $this->model->upsert($data, $uniqueBy, $update);
            if (! $result) {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }

    /**
     * Get query string that decrypt Aes256 for the provided statement.
     * The decryption happens on db execution.
     * Usage: search LIKE for a encrypted field
     *
     * @param string $rawStatement raw query (could be table field)
     * @return string|null the expression to get decrypted data
     */
    public function dbDecryptAes256($rawStatement) {
        try {
            $key = ValueUtil::get('common.aes_256_key');
            $iv = ValueUtil::get('common.aes_256_iv');
            DB::statement("SET SESSION block_encryption_mode = 'aes-256-cbc'");

            return "CAST(AES_DECRYPT(FROM_BASE64({$rawStatement}), UNHEX(SHA2('{$key}', 256)), FROM_BASE64('{$iv}')) AS CHAR)";
        } catch (Exception $e) {
            Log::error($e);
        }
    }
}
