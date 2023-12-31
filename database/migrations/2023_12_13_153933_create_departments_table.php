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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('department_name');
            $table->enum('employment_status', ['Contract of Service', 'Permanent']);
            $table->date('date_of_employment');
            $table->enum('job_title', ['Faculty', 'Office Staff', 'Utility Personnel', 'Watchman', 'Driver', 'College President', 'Dean', 'Office Head', 'Vice President for Administration', 'Vice President for Academic Affairs', 'HR Officer']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
