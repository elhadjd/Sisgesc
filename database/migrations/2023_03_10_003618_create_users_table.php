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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('apelido')->nullable();
            $table->unsignedBigInteger('companie_id')->nullable();
            $table->foreign('companie_id')->references('id')->on('companies')->onDelete('CASCADE');
            $table->string('nome_completo')->nullable();
            $table->string('email')->nullable();
            $table->string('imagem')->nullable();
            $table->enum('nivel',['user','admin'])->nullable()->default('user');
            $table->string('password')->nullable();
            $table->enum('estado',['active','inactive'])->nullable()->default('active');
            $table->timestamp('data')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
