<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update movements_view to use the almacen table instead of location_id columns
     * that were removed from products and consumables tables
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
                c.name AS client_name,
                NULL AS status,
                COALESCE(lm.name, la.name) AS location_name,
                la.sub_location,
                NULL AS unit,
                pr.name AS personnel_name,
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
            LEFT JOIN locations lm ON lm.id = em.location_id
            LEFT JOIN personnel pr ON pr.id = em.personnel_id
            LEFT JOIN almacen al ON al.product_id = p.id
            LEFT JOIN locations la ON la.id = al.location_id

            UNION ALL

            SELECT
                CONCAT('consumable-', cm.id) AS uid,
                'consumible' AS source,
                cm.id AS source_id,
                cm.type AS movement_type,
                cs.name AS item_name,
                c2.name AS client_name,
                cs.status,
                COALESCE(lm2.name, lac.name) AS location_name,
                alc.sub_location,
                cs.unit,
                pr2.name AS personnel_name,
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
            LEFT JOIN clients c2 ON c2.id = cm.client_id
            LEFT JOIN locations lm2 ON lm2.id = cm.location_id
            LEFT JOIN personnel pr2 ON pr2.id = cm.personnel_id
            LEFT JOIN almacen alc ON alc.consumable_id = cs.id
            LEFT JOIN locations lac ON lac.id = alc.location_id
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS movements_view');
        // Recreate the old view (this might fail if location_id columns don't exist)
    }
};
