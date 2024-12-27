<?php

namespace App\Support\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Str;

trait HasApiTokens
{
    public function tokens(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
//        return $this->morphMany(\Laravel\Sanctum\PersonalAccessToken::class, 'tokenable');
        return $this->morphMany(AccessToken::class, 'tokenable');
    }

    public function createToken(string $name, array $abilities = ['*'], ?\DateTimeInterface $expiresAt = null)
    {
        $plainTextToken = $this->generateTokenString();

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new Laravel\Sanctum\NewAccessToken($token, $token->getKey() . '|' . $plainTextToken);
    }

    public function generateTokenString(string $guard): string
    {
        return sprintf(
            '%s%s%s',
            config('tokens.' . $guard . '.token_prefix', ''),
            $tokenEntropy = Str::random(40),
            hash('crc32b', $tokenEntropy)
        );
    }
}