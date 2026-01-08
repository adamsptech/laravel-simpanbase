<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // Add missing columns
            if (!Schema::hasColumn('audit_logs', 'user_name')) {
                $table->string('user_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('audit_logs', 'model_type')) {
                $table->string('model_type')->after('action');
            }
            if (!Schema::hasColumn('audit_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            }
            if (!Schema::hasColumn('audit_logs', 'model_label')) {
                $table->string('model_label')->nullable()->after('model_id');
            }
            if (!Schema::hasColumn('audit_logs', 'old_values')) {
                $table->json('old_values')->nullable()->after('model_label');
            }
            if (!Schema::hasColumn('audit_logs', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values');
            }
            if (!Schema::hasColumn('audit_logs', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('new_values');
            }
            if (!Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip_address');
            }

            // Drop old session_id column if exists
            if (Schema::hasColumn('audit_logs', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });

        // Add indexes
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasIndex('audit_logs', 'audit_logs_model_type_model_id_index')) {
                $table->index(['model_type', 'model_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn([
                'user_name', 'model_type', 'model_id', 'model_label',
                'old_values', 'new_values', 'ip_address', 'user_agent'
            ]);
            $table->string('session_id', 300)->nullable();
        });
    }
};
