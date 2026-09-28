<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('juri')->after('email');
        });

        Schema::create('lomba', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi')->nullable();
            $table->string('status')->default('draft'); // draft, aktif, dikunci
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('kriteria_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lomba_id')->constrained('lomba')->cascadeOnDelete();
            $table->string('nama_kriteria');
            $table->decimal('bobot', 5, 2)->default(1);
            $table->unsignedInteger('skala_max')->default(100);
            $table->timestamps();
        });

        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lomba_id')->constrained('lomba')->cascadeOnDelete();
            $table->string('nomor_urut');
            $table->string('nama');
            $table->string('asal_kelas');
            $table->timestamps();
        });

        Schema::create('juri_lomba', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lomba_id')->constrained('lomba')->cascadeOnDelete();
            $table->unique(['user_id', 'lomba_id']);
            $table->timestamps();
        });

        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('juri_lomba_id')->constrained('juri_lomba')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->foreignId('kriteria_id')->constrained('kriteria_penilaian')->cascadeOnDelete();
            $table->decimal('skor', 8, 2);
            $table->unique(['juri_lomba_id', 'peserta_id', 'kriteria_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('juri_lomba');
        Schema::dropIfExists('peserta');
        Schema::dropIfExists('kriteria_penilaian');
        Schema::dropIfExists('lomba');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
