<?php
/**
 * GEO Authority Suite - Entity Registry
 */

if (!defined('ABSPATH')) {
    exit;
}

global $geo_entities_registry;
global $geo_entities_locked;
$geo_entities_registry = [];
$geo_entities_locked = [];

function geo_register_entity(array $entity, bool $lock = false): void {
    global $geo_entities_registry;
    global $geo_entities_locked;

    if (empty($entity['@id'])) {
        return;
    }

    if (isset($entity['@context'])) {
        unset($entity['@context']);
    }

    if (isset($entity['@type']) && in_array($entity['@type'], ['worksFor', 'memberOf'])) {
        return;
    }

    $id = $entity['@id'];

    if (isset($geo_entities_locked[$id]) && $geo_entities_locked[$id] === true) {
        return;
    }

    $geo_entities_registry[$id] = $entity;

    if ($lock) {
        $geo_entities_locked[$id] = true;
    }
}

function geo_register_entity_locked(array $entity): void {
    geo_register_entity($entity, true);
}

function geo_get_entities(): array {
    global $geo_entities_registry;
    return $geo_entities_registry ?? [];
}

function geo_get_entity(string $id): ?array {
    global $geo_entities_registry;
    return $geo_entities_registry[$id] ?? null;
}

function geo_count_entities(): int {
    global $geo_entities_registry;
    return count($geo_entities_registry ?? []);
}

function geo_reset_entities(): void {
    global $geo_entities_registry;
    $geo_entities_registry = [];
}

function geo_get_entities_by_type(string $type): array {
    $entities = geo_get_entities();
    return array_filter($entities, function ($entity) use ($type) {
        return isset($entity['@type']) && $entity['@type'] === $type;
    });
}
