<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as HttpCookie;

class UserService
{
    public function generateToken(): string
    {
        return hash('sha256', Str::random(64).microtime());
    }

    public function createNameOnly(string $name): array
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            throw new \InvalidArgumentException('Nama tidak boleh kosong');
        }

        $user = new User;
        $user->name = $trimmed;
        $token = $this->generateToken();
        $user->remember_token = $token;
        $user->save();

        $expiresAt = Carbon::now()->addYear();

        return [
            'user' => $user,
            'token' => $token,
            'expires_at' => $expiresAt,
        ];
    }

    public function makeAuthCookie(string $token): HttpCookie
    {
        $minutes = 60 * 24 * 365;

        return cookie('user_token', $token, $minutes, '/', null, false, false, false, 'lax');
    }

    public function updateSetupType(User $user, string $type): User
    {
        $allowed = ['new', 'dummy'];
        if (! in_array($type, $allowed, true)) {
            throw new \InvalidArgumentException('Setup type tidak valid');
        }
        $user->setup_type = $type;
        $user->save();

        if ($type === 'dummy') {
            $this->seedDummyTransactions($user->id, 6);
        }

        return $user;
    }

    protected function seedDummyTransactions(string $userId, int $months = 6): void
    {
        $end = Carbon::today();
        $start = (clone $end)->subMonthsNoOverflow($months);

        $notesIn = [
            'Penjualan harian',
            'Pembayaran pelanggan',
            'Pendapatan layanan',
            'Penjualan produk',
        ];
        $notesOut = [
            'Belanja bahan',
            'Biaya operasional',
            'Transportasi',
            'Pembayaran supplier',
        ];

        $rows = [];
        $cursor = (clone $start);
        while ($cursor->lte($end)) {
            $date = $cursor->format('Y-m-d');

            $inCount = $cursor->day % 3 === 0 ? 1 : 0;
            $outCount = 1;

            for ($i = 0; $i < $inCount; $i++) {
                $rows[] = [
                    'user_id' => $userId,
                    'type' => 'IN',
                    'amount' => random_int(100000, 500000),
                    'note' => $notesIn[array_rand($notesIn)],
                    'date' => $date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            for ($i = 0; $i < $outCount; $i++) {
                $rows[] = [
                    'user_id' => $userId,
                    'type' => 'OUT',
                    'amount' => random_int(20000, 200000),
                    'note' => $notesOut[array_rand($notesOut)],
                    'date' => $date,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }

            $cursor->addDay();
        }

        if (! empty($rows)) {
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('transactions')->insert($chunk);
            }
        }
    }

    public function getByToken(?string $token): ?User
    {
        if (! $token) {
            return null;
        }

        return User::where('remember_token', $token)->first();
    }
}
