<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class NoCashiRemnantTest extends TestCase
{
    public function test_env_example_uses_ipaymu_keys(): void
    {
        $content = file_get_contents(__DIR__ . '/../../.env.example');

        $this->assertStringContainsString('IPAYMU_VA=', $content);
        $this->assertStringContainsString('IPAYMU_API_KEY=', $content);
        $this->assertStringContainsString('IPAYMU_BASE_URL=https://sandbox.ipaymu.com', $content);
        $this->assertStringNotContainsString('CASHI_', $content);
    }

    public function test_controllers_have_no_cashi_or_xendit_references(): void
    {
        foreach ([
            __DIR__ . '/../../app/Http/Controllers/PublicController.php',
            __DIR__ . '/../../app/Http/Controllers/PaymentController.php',
            __DIR__ . '/../../routes/api.php',
        ] as $file) {
            $content = file_get_contents($file);
            $this->assertStringNotContainsStringIgnoringCase('cashi', $content, "Cashi remnant in $file");
            $this->assertStringNotContainsStringIgnoringCase('xendit', $content, "Xendit remnant in $file");
        }
    }
}
