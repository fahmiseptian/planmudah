<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id('id_task');
            $table->unsignedBigInteger('id_project');
            $table->string('name', 255);
            $table->enum('status', ['on_process', 'complete', 'pending', 'expired'])->default('pending');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key
            $table->foreign('id_project')->references('id_project')->on('projects')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('tasks');
    }
};
