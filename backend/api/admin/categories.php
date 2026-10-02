<?php
// categories.php — GET/POST/DELETE: Sport categories CRUD
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../auth/guard.php';

$user = guardRole('admin');
$db = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // List all categories
    $stmt = $db->query('SELECT * FROM sport_categories ORDER BY name');
    $categories = $stmt->fetchAll();
    foreach ($categories as &$cat) {
        $cat['equipment'] = json_decode($cat['equipment'] ?? '[]', true);
    }
    jsonResponse(['categories' => $categories]);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create or update category
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['name'])) {
        jsonError('Category name is required');
    }

    if (isset($input['id']) && $input['id']) {
        // Update
        $stmt = $db->prepare(
            'UPDATE sport_categories SET name = ?, icon = ?, field_size = ?, equipment = ?, is_active = ?
             WHERE id = ?'
        );
        $stmt->execute([
            $input['name'],
            $input['icon'] ?? null,
            $input['field_size'] ?? null,
            json_encode($input['equipment'] ?? []),
            $input['is_active'] ?? 1,
            (int) $input['id']
        ]);
        jsonResponse(['success' => true, 'message' => 'Category updated']);
    } else {
        // Insert
        $stmt = $db->prepare(
            'INSERT INTO sport_categories (name, icon, field_size, equipment) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $input['name'],
            $input['icon'] ?? null,
            $input['field_size'] ?? null,
            json_encode($input['equipment'] ?? [])
        ]);
        jsonResponse(['success' => true, 'category_id' => (int) $db->lastInsertId()]);
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $catId = (int) ($input['id'] ?? 0);
    if ($catId) {
        $stmt = $db->prepare('DELETE FROM sport_categories WHERE id = ?');
        $stmt->execute([$catId]);
    }
    jsonResponse(['success' => true]);
}
