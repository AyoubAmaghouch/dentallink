<?php
declare(strict_types=1);

final class DentalSchema
{
    private array $columns;
    private array $statuses;

    private function __construct(array $columns, array $statuses) { $this->columns = $columns; $this->statuses = $statuses; }

    public static function inspect(PDO $pdo): self
    {
        $columns = [];
        foreach (['laboratoire','service','laboratoire_service','image_laboratoire','admin'] as $table) {
            try { $columns[$table] = $pdo->query('SHOW COLUMNS FROM `' . $table . '`')->fetchAll(PDO::FETCH_ASSOC); }
            catch (PDOException $e) { $columns[$table] = []; }
        }
        $statuses = [];
        foreach ($columns['laboratoire'] as $column) if ($column['Field'] === 'statut' && preg_match_all("/'((?:[^']|\\\\')*)'/", (string)$column['Type'], $m)) $statuses = $m[1];
        return new self($columns, $statuses);
    }

    public function has(string $table, string $column): bool { foreach ($this->columns[$table] ?? [] as $item) if ($item['Field'] === $column) return true; return false; }
    public function field(string $table, array $candidates): string
    {
        foreach ($candidates as $candidate) if ($this->has($table, $candidate)) return $candidate;
        throw new RuntimeException('Le schéma SQL ne contient aucune colonne attendue pour ' . $table . '.');
    }
    public function labId(): string { return $this->field('laboratoire', ['id','id_laboratoire']); }
    public function serviceId(): string { return $this->field('service', ['id','id_service']); }
    public function serviceName(): string { return $this->field('service', ['nom','nom_service']); }
    public function imageId(): string { return $this->field('image_laboratoire', ['id','id_image']); }
    public function imagePath(): string { return $this->field('image_laboratoire', ['chemin','url_image']); }
    public function adminId(): string { return $this->field('admin', ['id','id_admin']); }
    public function hasDocument(): bool { return $this->has('laboratoire', 'document_verification'); }
    public function hasCreatedAt(string $table = 'laboratoire'): bool { return $this->has($table, 'created_at'); }
    public function status(string $canonical): string
    {
        $fallback = ['en_attente'=>'en_attente','valide'=>'accepte','rejete'=>'refuse'];
        if (in_array($canonical, $this->statuses, true)) return $canonical;
        return $fallback[$canonical] ?? $canonical;
    }
    public function canonicalStatus(string $stored): string { return ['accepte'=>'valide','refuse'=>'rejete'][$stored] ?? $stored; }
    public function statusLabel(string $stored): string { return ['en_attente'=>'En attente','valide'=>'Validé','accepte'=>'Validé','rejete'=>'Rejeté','refuse'=>'Rejeté'][$stored] ?? $stored; }
}

function dental_schema(PDO $pdo): DentalSchema { static $schema; return $schema ??= DentalSchema::inspect($pdo); }
