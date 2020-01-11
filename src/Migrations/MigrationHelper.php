<?php

namespace Antriver\LaravelSiteScaffolding\Migrations;

use Illuminate\Database\Schema\Blueprint;

class MigrationHelper
{
    /**
     * Adds timestamps to a time with the correct names
     * createdAt and updatedAt instead of created_at updated_at
     *
     * @param Blueprint $table
     */
    public static function addTimestamps(Blueprint $table)
    {
        static::addCreatedAt($table);
        static::addUpdatedAt($table);
    }

    public static function addCreatedAt(Blueprint $table)
    {
        $table->dateTime('createdAt')->default(\DB::raw('CURRENT_TIMESTAMP'));
    }

    public static function addUpdatedAt(Blueprint $table)
    {
        $table->dateTime('updatedAt')->nullable();
    }

    public static function addDeletedAt(Blueprint $table)
    {
        $table->dateTime('deletedAt')->nullable();
    }
}
