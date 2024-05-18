<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the 'name' column
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Ensure the specified columns are in the correct order after 'id'
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->string('phone')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->date('birthdate')->nullable()->change();
            $table->text('bio')->nullable()->change();
        });

        // Move the columns to be after 'id' using raw SQL as Laravel schema builder does not support 'after' directly on 'change'
        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE users MODIFY first_name VARCHAR(255) NOT NULL AFTER id');
            DB::statement('ALTER TABLE users MODIFY last_name VARCHAR(255) NOT NULL AFTER first_name');
            DB::statement('ALTER TABLE users MODIFY phone VARCHAR(255) NULL AFTER last_name');
            DB::statement('ALTER TABLE users MODIFY country VARCHAR(255) NULL AFTER phone');
            DB::statement('ALTER TABLE users MODIFY birthdate DATE NULL AFTER country');
            DB::statement('ALTER TABLE users MODIFY bio TEXT NULL AFTER birthdate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add the 'name' column back
            $table->string('name')->after('id');

            // Revert the changes made to the columns
            $table->string('first_name')->change();
            $table->string('last_name')->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('country')->nullable(false)->change();
            $table->date('birthdate')->nullable(false)->change();
            $table->text('bio')->nullable(false)->change();
        });
    }
};
