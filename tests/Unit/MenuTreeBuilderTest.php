<?php

namespace Tests\Unit;

use App\Services\MenuTreeBuilder;
use PHPUnit\Framework\TestCase;

class MenuTreeBuilderTest extends TestCase
{
    public function test_builds_nested_tree_from_flat_meta_parent(): void
    {
        $flat = [
            ['ID' => 106, 'meta_parent' => 0, 'order' => 4, 'title' => 'Gallery'],
            ['ID' => 200, 'meta_parent' => 106, 'order' => 1, 'title' => 'Foto'],
            ['ID' => 201, 'meta_parent' => 106, 'order' => 2, 'title' => 'Video'],
            ['ID' => 300, 'meta_parent' => 200, 'order' => 1, 'title' => 'Foto 2024'],
        ];

        $tree = MenuTreeBuilder::toTree($flat);

        $this->assertCount(1, $tree);
        $this->assertEquals(106, $tree[0]['item']['ID']);
        $this->assertCount(2, $tree[0]['children']);
        $this->assertEquals(200, $tree[0]['children'][0]['item']['ID']);
        $this->assertCount(1, $tree[0]['children'][0]['children']);
        $this->assertEquals(300, $tree[0]['children'][0]['children'][0]['item']['ID']);
    }

    public function test_orphan_and_cycle_do_not_infinite_loop(): void
    {
        $flat = [
            ['ID' => 1, 'meta_parent' => 0, 'order' => 1, 'title' => 'Top'],
            ['ID' => 2, 'meta_parent' => 999, 'order' => 2, 'title' => 'Orphan'],
            ['ID' => 3, 'meta_parent' => 4, 'order' => 3, 'title' => 'A'],
            ['ID' => 4, 'meta_parent' => 3, 'order' => 4, 'title' => 'B'],
        ];

        $tree = MenuTreeBuilder::toTree($flat);

        // Orphan + cycle ditampung, tidak infinite, top tetap ada
        $this->assertNotEmpty($tree);
    }
}
