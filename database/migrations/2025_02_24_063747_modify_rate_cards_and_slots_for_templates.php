<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, create a backup of existing rate cards
        Schema::create('rate_cards_backup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_slot_owner_id');
            $table->foreignId('slot_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('hour_block');
            $table->integer('rate');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Copy existing data to backup
        DB::statement('INSERT INTO rate_cards_backup SELECT * FROM rate_cards');

        // Modify rate_cards table
        Schema::table('rate_cards', function (Blueprint $table) {
            $table->boolean('is_template')->default(false)->after('is_active');
            $table->dropForeign(['slot_id']);
            $table->dropColumn('slot_id');
        });

        // Add rate_card_id to slots table
        Schema::table('slots', function (Blueprint $table) {
            $table->foreignId('rate_card_id')->nullable()->constrained()->nullOnDelete();
        });

        // Migrate existing rate cards to new structure
        DB::statement('
            UPDATE slots s
            SET rate_card_id = rcb.id
            FROM rate_cards_backup rcb
            WHERE rcb.slot_id = s.id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slots', function (Blueprint $table) {
            $table->dropForeign(['rate_card_id']);
            $table->dropColumn('rate_card_id');
        });

        Schema::table('rate_cards', function (Blueprint $table) {
            $table->dropColumn('is_template');
            $table->foreignId('slot_id')->constrained();
        });

        // Restore data from backup
        DB::statement('UPDATE rate_cards SET slot_id = (SELECT slot_id FROM rate_cards_backup WHERE rate_cards_backup.id = rate_cards.id)');

        Schema::dropIfExists('rate_cards_backup');
    }
};
