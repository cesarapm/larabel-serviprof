<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS movements_view');

        DB::statement(<<<'SQL'
            CREATE VIEW movements_view AS
            SELECT
                CONCAT('equipment-', em.id) AS uid,
                'equipo' AS source,
                em.id AS source_id,
                em.type AS movement_type,
                CONCAT_WS(' ', p.brand, p.model, CONCAT('#', p.serial_number)) AS item_name,
                c.name AS related_name,
                NULL AS quantity,
                em.current_counter_bw,
                em.current_counter_color,
                em.counter_read_at,
                COALESCE(em.date_out, DATE(em.created_at)) AS movement_date,
                em.date_return,
                em.notes,
                em.created_at,
                em.updated_at
            FROM equipment_movements em
            LEFT JOIN products p ON p.id = em.product_id
            LEFT JOIN clients c ON c.id = em.client_id

            UNION ALL

            SELECT
                CONCAT('consumable-', cm.id) AS uid,
                'consumible' AS source,
                cm.id AS source_id,
                cm.type AS movement_type,
                cs.name AS item_name,
                NULL AS related_name,
                cm.quantity,
                NULL AS current_counter_bw,
                NULL AS current_counter_color,
                NULL AS counter_read_at,
                cm.movement_date,
                NULL AS date_return,
                cm.notes,
                cm.created_at,
                cm.updated_at
            FROM consumable_movements cm
            LEFT JOIN consumables cs ON cs.id = cm.consumable_id
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS movements_view');
    }
};
