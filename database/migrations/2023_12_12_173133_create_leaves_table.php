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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
            $table->datetime('date_approved')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->enum('type', ['Vacation', 'Forced', 'Sick', 'Maternity', 'Paternity', 'Special Privilege', 'Solo Parent', 'Study', 'VAWC', 'Rehabilitation', 'Special Leave Benefits For Women', 'Special Emergency', 'Exit Pass']);
            $table->enum('status', ['Waiting', 'Submitted To Admin', 'Approved', 'Rejected']);
            $table->longText('remarks')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_type')->nullable();
            $table->string('attachment_size')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
