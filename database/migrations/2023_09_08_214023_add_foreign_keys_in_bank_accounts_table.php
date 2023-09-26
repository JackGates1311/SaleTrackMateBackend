<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {

            $table->foreignUuid('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->foreignUuid('recipient_id')->nullable();
            $table->foreign('recipient_id')->references('id')->on('recipients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
