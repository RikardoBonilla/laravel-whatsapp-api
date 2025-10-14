<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('keyword_rules')) {
            if (config('database.default') === 'sqlite') {
                $this->upgradeForSqlite();
            } else {
                $this->upgradeForMysql();
            }
        } else {
            Schema::create('keyword_rules', function (Blueprint $table) {
                $table->id();
                $table->json('keywords');
                $table->boolean('fuzzy_match')->default(false);
                $table->string('trigger_type')->default('contains');
                $table->json('variables')->nullable();
                $table->string('response');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active']);
            });
        }
    }

    private function upgradeForSqlite(): void
    {
        Schema::dropIfExists('keyword_rules_backup');
        DB::statement('CREATE TABLE keyword_rules_backup AS SELECT * FROM keyword_rules');

        Schema::dropIfExists('keyword_rules');
        Schema::create('keyword_rules', function (Blueprint $table) {
            $table->id();
            $table->json('keywords');
            $table->boolean('fuzzy_match')->default(false);
            $table->string('trigger_type')->default('contains');
            $table->json('variables')->nullable();
            $table->string('response');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active']);
        });

        if (Schema::hasTable('keyword_rules_backup')) {
            DB::statement("INSERT INTO keyword_rules (id, keywords, response, is_active, created_at, updated_at)
                          SELECT id, JSON_ARRAY(keyword), response_template, is_active, created_at, updated_at
                          FROM keyword_rules_backup");
            Schema::dropIfExists('keyword_rules_backup');
        }
    }

    private function upgradeForMysql(): void
    {
        Schema::table('keyword_rules', function (Blueprint $table) {
            $table->json('keywords')->after('keyword');
            $table->boolean('fuzzy_match')->default(false)->after('keywords');
            $table->string('trigger_type')->default('contains')->after('fuzzy_match');
            $table->json('variables')->nullable()->after('trigger_type');
            $table->renameColumn('response_template', 'response');
        });

        DB::statement("UPDATE keyword_rules SET keywords = JSON_ARRAY(keyword)");

        Schema::table('keyword_rules', function (Blueprint $table) {
            $table->dropColumn('keyword');
        });
    }

    public function down(): void
    {
        Schema::table('keyword_rules', function (Blueprint $table) {
            $table->string('keyword')->after('id');
            $table->dropColumn(['keywords', 'fuzzy_match', 'trigger_type', 'variables']);
        });
    }
};