-- Solo consulta: lista las migraciones anteriores (fases D a H) que faltan en esta base.
-- Si devuelve filas, ejecutar antes los archivos fase-d-*, fase-e-*, fase-f-*, fase-h-* de administrativo/admin/db.
SELECT n.nombre AS prerrequisito_faltante
FROM (
  SELECT 'fase-d-001-rbac-schema' AS nombre UNION ALL SELECT 'fase-d-002-rbac-seed'
  UNION ALL SELECT 'fase-e-001-ia-schema' UNION ALL SELECT 'fase-e-002-ia-role-assign'
  UNION ALL SELECT 'fase-e-003-ia-voz-schema' UNION ALL SELECT 'fase-e-004-ia-voz-role-assign'
  UNION ALL SELECT 'fase-f-001-espacio-geografico-soft-delete' UNION ALL SELECT 'fase-f-002-ficha-tecnica-soft-delete'
  UNION ALL SELECT 'fase-f-003-sondeos-soft-delete'
  UNION ALL SELECT 'fase-h-001-certificacion-revision-schema' UNION ALL SELECT 'fase-h-002-certificacion-revision-rbac'
) n
LEFT JOIN tbl_migraciones_ejecutadas m ON m.nombre_migracion = n.nombre
WHERE m.id IS NULL;
