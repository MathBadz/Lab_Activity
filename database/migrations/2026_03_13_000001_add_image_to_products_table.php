<?php

use Illuminate\Database\Migrations\Migration;

// This migration is no longer needed — image column was consolidated
// into the original create_products_table migration.
return new class extends Migration
{
    public function up(): void
    {
        // No-op: image column is now in the original migration
    }

    public function down(): void
    {
        // No-op
    }
};
