<?php

namespace App\Traits;

use Illuminate\Database\Schema\Blueprint;

trait DatabaseCommonTrait
{
    /**
     * Adds common columns to a database table.
     *
     * @param Blueprint $table The table to add columns to.
     * @return void
     */
    public function commonColumns(Blueprint $table) {
        $table->tinyInteger('del_flg')->default(0)->nullable();

        $table->dateTime('created_at')->nullable();
        $table->unsignedBigInteger('created_by')->nullable();

        $table->dateTime('updated_at')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();

        $table->dateTime('deleted_at')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();
    }

    public function commonCharset(Blueprint $table) {
        $table->charset = 'utf8mb4';
        $table->collation = 'utf8mb4_general_ci';
    }
}
