<?php
if (! Schema::hasColumns('table', ['model_type', 'model_ids'])) {
    Schema::table('table', function (Blueprint $table) {
        $table->string('model_type');
        $table->json('model_ids');
        $table->rawIndex('((CAST(model_ids->"$[*]" AS UNSIGNED ARRAY))), model_type', 'model_ids_model_type');
    });
}