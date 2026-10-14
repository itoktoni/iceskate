<?php

namespace Tests\Unit;

use App\Http\Controllers\PublicController;
use PHPUnit\Framework\TestCase;

class IpaymuSignatureTest extends TestCase
{
    private function callSignature(array $body): array
    {
        $method = new \ReflectionMethod(PublicController::class, 'ipaymuSignature');
        $method->setAccessible(true);

        return $method->invoke(null, $body);
    }

    private function withTestCredentials(): void
    {
        putenv('IPAYMU_VA=1179000899');
        $_ENV['IPAYMU_VA'] = '1179000899';
        putenv('IPAYMU_API_KEY=TESTKEY123');
        $_ENV['IPAYMU_API_KEY'] = 'TESTKEY123';
    }

    public function test_signature_matches_official_vector(): void
    {
        $body = [
            'product' => ['T-Shirt'],
            'qty' => ['1'],
            'price' => ['100000'],
            'description' => ['Blue Size L'],
            'returnUrl' => 'https://example.com/thank-you',
            'notifyUrl' => 'https://example.com/callback',
            'cancelUrl' => 'https://example.com/cancel',
            'referenceId' => 'INV-002',
        ];

        $this->withTestCredentials();

        $result = $this->callSignature($body);

        $this->assertSame(
            '{"product":["T-Shirt"],"qty":["1"],"price":["100000"],"description":["Blue Size L"],'
            . '"returnUrl":"https://example.com/thank-you","notifyUrl":"https://example.com/callback",'
            . '"cancelUrl":"https://example.com/cancel","referenceId":"INV-002"}',
            $result['json']
        );
        $this->assertSame('010838ef8c75da854f055c9b9e2226a8e16e881533ddf973b7da44e8db13a517', $result['signature']);
        $this->assertMatchesRegularExpression('/^\d{14}$/', $result['timestamp']);
    }

    public function test_signature_changes_when_body_changes(): void
    {
        $this->withTestCredentials();

        $a = $this->callSignature(['referenceId' => 'A']);
        $b = $this->callSignature(['referenceId' => 'B']);

        $this->assertNotSame($a['signature'], $b['signature']);
    }
}
