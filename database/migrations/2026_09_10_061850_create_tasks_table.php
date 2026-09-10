<?php

use App\Enum\Priority;
use App\Enum\Status;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignUlid('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->enum(
                'priority',
                array_column(Priority::cases(), 'value')
            )->default(Priority::RENDAH->value);

            $table->enum(
                'status',
                array_column(Status::cases(), 'value')
            )->default(Status::BELUM_DIMULAI->value);

            $table->date('due_date')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'priority']);
            $table->index(['category_id']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
