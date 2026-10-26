<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/schema.php';
require_admin();

function admin_flash(string $type, string $message): void { $_SESSION['admin_flash'] = ['type' => $type, 'message' => $message]; }
function admin_take_flash(): ?array { $flash = $_SESSION['admin_flash'] ?? null; unset($_SESSION['admin_flash']); return is_array($flash) ? $flash : null; }
function admin_redirect(string $url): never { header('Location: ' . $url); exit; }
function admin_valid_services(PDO $pdo, array $input): array {
    $ids = array_values(array_unique(array_filter(array_map('intval', $input), static fn(int $id): bool => $id > 0)));
    if ($ids) { $column = dental_schema($pdo)->serviceId(); $marks = implode(',', array_fill(0, count($ids), '?')); $q = $pdo->prepare("SELECT `$column` FROM service WHERE `$column` IN ($marks)"); $q->execute($ids); if (count($q->fetchAll(PDO::FETCH_COLUMN)) !== count($ids)) throw new RuntimeException('Un ou plusieurs services sont invalides.'); }
    return $ids;
}
function admin_validate_lab(array $input, bool $new = false): array {
    $data = [];
    foreach (['nom_laboratoire','nom_gerant','telephone','email','adresse','ville','description'] as $field) { $data[$field] = trim((string)($input[$field] ?? '')); if ($data[$field] === '') throw new RuntimeException('Tous les champs obligatoires doivent être renseignés.'); }
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Adresse e-mail invalide.');
    validate_lab_field_lengths($data);
    foreach (['site_web','instagram'] as $field) { $url = trim((string)($input[$field] ?? '')); if ($url !== '' && (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(strtolower((string)parse_url($url, PHP_URL_SCHEME)), ['http','https'], true))) throw new RuntimeException('Les liens web doivent être des URL HTTP ou HTTPS valides.'); $data[$field] = $url ?: null; }
    validate_lab_field_lengths($data);
    if ($new) { $password = (string)($input['mot_de_passe'] ?? ''); if (strlen($password) < 10) throw new RuntimeException('Le mot de passe initial doit contenir au moins 10 caractères.'); $data['mot_de_passe'] = password_hash($password, PASSWORD_DEFAULT); }
    return $data;
}
function admin_file_path(?string $relative, string $folder): ?string {
    if (!$relative) return null;
    $base = realpath(dirname(__DIR__) . '/assets/uploads/' . $folder); $file = realpath(dirname(__DIR__) . '/' . $relative);
    return $base && $file && str_starts_with($file, $base . DIRECTORY_SEPARATOR) && is_file($file) ? $file : null;
}
function admin_delete_file(?string $relative, string $folder): void { $path = admin_file_path($relative, $folder); if ($path) unlink($path); }
function admin_status_label(string $status): string { return dental_schema(Database::connection())->statusLabel($status); }
