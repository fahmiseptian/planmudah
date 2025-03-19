<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('projects', function (Blueprint $table) {
            $table->id('id_project');
            $table->string('code_project', 20)->unique();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['ongoing', 'pending', 'finished'])->default('pending');
            $table->string('cover')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        Schema::dropIfExists('projects');
    }
};
