<?php

namespace App\Services;

class MenuTreeBuilder
{
    /**
     * Fold flat list (ID, meta_parent, order) jadi tree bersarang.
     * Pure function — tanpa DB, aman di-unit-test.
     *
     * @param array $flat  tiap elemen: ['ID'=>int,'meta_parent'=>int,'order'=>int, ...]
     * @return array tiap node: ['item'=>array,'children'=>array]
     */
    public static function toTree(array $flat, int $parentId = 0, array $visited = []): array
    {
        if (in_array($parentId, $visited, true)) {
            return [];
        }
        $visited[] = $parentId;

        $children = array_values(array_filter(
            $flat,
            fn($r) => (int) ($r['meta_parent'] ?? 0) === $parentId
        ));
        usort($children, fn($a, $b) => ((int) ($a['order'] ?? 0)) <=> ((int) ($b['order'] ?? 0)));

        $tree = [];
        foreach ($children as $child) {
            $id = (int) ($child['ID'] ?? 0);
            if (in_array($id, $visited, true)) {
                $tree[] = ['item' => $child, 'children' => []];
                continue;
            }
            $tree[] = [
                'item' => $child,
                'children' => self::toTree($flat, $id, $visited),
            ];
        }

        // Root call: tampung orphan + siklus agar tidak hilang diam-diam
        if ($parentId === 0 && count($visited) === 1) {
            $seen = [];
            $collect = function ($nodes) use (&$collect, &$seen) {
                foreach ($nodes as $n) {
                    $seen[(int) ($n['item']['ID'] ?? 0)] = true;
                    $collect($n['children']);
                }
            };
            $collect($tree);
            $allIds = [];
            foreach ($flat as $r) {
                $allIds[(int) ($r['ID'] ?? 0)] = true;
            }
            foreach ($flat as $r) {
                $id = (int) ($r['ID'] ?? 0);
                if (!isset($seen[$id])) {
                    $tree[] = ['item' => $r, 'children' => [], 'orphan' => true];
                }
            }
        }

        return $tree;
    }

    /**
     * Normalisasi koleksi Corcel MenuItem jadi flat array untuk toTree.
     * Dipanggil di Blade runtime (production), bukan di test.
     */
    public static function fromCorcel($items): array
    {
        $flat = [];
        foreach ($items ?? [] as $it) {
            $metaParent = 0;
            try {
                $metaParent = (int) ($it->meta->_menu_item_menu_item_parent ?? 0);
            } catch (\Throwable $e) {
                $metaParent = 0;
            }
            $flat[] = [
                'ID' => (int) $it->ID,
                'meta_parent' => $metaParent,
                'order' => (int) ($it->menu_order ?? 0),
                '_model' => $it,
            ];
        }
        return $flat;
    }
}
