<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->text('link');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });

        DB::statement('create index links_deleted_at_is_null_index on links(deleted_at) where deleted_at is null');
        DB::statement('create index links_deleted_at_is_not_null_index on links(deleted_at) where deleted_at is not null');
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
