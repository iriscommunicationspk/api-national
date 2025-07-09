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
        Schema::create('user_requests', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('department');
            $table->string('name');
            //for long strings
            $table->text('researchRequirement');
            $table->text('scope');
            $table->string('status')->default('pending');
            $table->text('adminResponse')->nullable();
            $table->timestamps();
        });

        Schema::table('user_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_requests');
    }
};
