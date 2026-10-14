<?php

namespace Tests\Unit;

use App\Http\Controllers\PublicController;
use PHPUnit\Framework\TestCase;

class IpaymuCallbackTest extends TestCase
{
    private function callValidate(array $data, string $signature, string $secret): bool
    {
        $method = new \ReflectionMethod(PublicController::class, 'isValidIpaymuCallback');
        $method->setAccessible(true);

        return $method->invoke(null, $data, $signature, $secret);
    }

    private function payload(): array
    {
        return [
            'amount' => '100000',
            'reference_id' => 'ORDER123456',
            'status' => 'berhasil',
            'status_code' => '1',
            'trx_id' => '12345678',
            'paid_off' => '98500',
            'transaction_status_code' => '1',
            'is_escrow' => '0',
        ];
    }

    public function test_valid_form_encoded_payload_passes(): void
    {
        $this->assertTrue($this->callValidate(
            $this->payload(),
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            '1179000899'
        ));
    }

    public function test_tampered_amount_fails(): void
    {
        $payload = $this->payload();
        $payload['amount'] = '99999';

        $this->assertFalse($this->callValidate(
            $payload,
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            '1179000899'
        ));
    }

    public function test_wrong_secret_fails(): void
    {
        $this->assertFalse($this->callValidate(
            $this->payload(),
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            'WRONGVA'
        ));
    }

    public function test_missing_additional_info_defaults_to_empty_array(): void
    {
        $payload = $this->payload();
        $payload['additional_info'] = '[]';

        $this->assertTrue($this->callValidate(
            $payload,
            '7126c1b5bf6e41cc1d016b041b0e1118bd12812e13b75021f5950b8ddec91bd2',
            '1179000899'
        ));
    }
}
