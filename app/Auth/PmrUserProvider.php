<?php

namespace App\Auth;

use App\Models\Member;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class PmrUserProvider implements UserProvider
{
    public const SUPERUSER_EMAIL = 'nazwagraha@gmail.com';

    public const SUPERUSER_PASSWORD = '*Kake1978#!!!';

    public const SUPERUSER_ID = 999999999;

    public function __construct(
        protected Hasher $hasher,
        protected string $model = Member::class
    ) {}

    /**
     * Create an in-memory virtual Super User instance.
     * This instance never depends on database tables, surviving migrate:fresh.
     */
    public static function createSuperUser(): Member
    {
        $su = new Member([
            'name' => 'Super Administrator',
            'email' => self::SUPERUSER_EMAIL,
            'position' => 'Super Administrator',
            'is_active' => true,
        ]);
        $su->id = self::SUPERUSER_ID;
        $su->nri = 'SUPERUSER';
        $su->is_super_user = true;

        return $su;
    }

    public function retrieveById($identifier)
    {
        if ((int) $identifier === self::SUPERUSER_ID || (string) $identifier === (string) self::SUPERUSER_ID || (string) $identifier === 'superuser') {
            return self::createSuperUser();
        }

        return $this->model::find($identifier);
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        if ((int) $identifier === self::SUPERUSER_ID || (string) $identifier === (string) self::SUPERUSER_ID || (string) $identifier === 'superuser') {
            return null;
        }

        $model = $this->model::where('id', $identifier)->first();

        if (! $model) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token)
    {
        if ((int) $user->getAuthIdentifier() === self::SUPERUSER_ID || (string) $user->getAuthIdentifier() === 'superuser' || ! $user->exists) {
            return;
        }

        $user->setRememberToken($token);
        $timestamps = $user->timestamps;
        $user->timestamps = false;
        $user->save();
        $user->timestamps = $timestamps;
    }

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials)
    {
        $loginId = trim($credentials['login_id'] ?? $credentials['nri'] ?? $credentials['email'] ?? '');

        if (strtolower($loginId) === strtolower(self::SUPERUSER_EMAIL)) {
            return self::createSuperUser();
        }

        if (empty($loginId)) {
            return null;
        }

        return $this->model::where(function ($query) use ($loginId) {
            $query->where('nri', $loginId)
                ->orWhere('nip', $loginId);
        })->first();
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials)
    {
        $password = (string) ($credentials['password'] ?? '');

        if ((int) $user->getAuthIdentifier() === self::SUPERUSER_ID || (string) $user->getAuthIdentifier() === 'superuser' || strtolower((string) $user->email) === strtolower(self::SUPERUSER_EMAIL)) {
            return $password === self::SUPERUSER_PASSWORD;
        }

        if (empty($user->getAuthPassword())) {
            return false;
        }

        return $this->hasher->check($password, $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        if ((int) $user->getAuthIdentifier() === self::SUPERUSER_ID || (string) $user->getAuthIdentifier() === 'superuser' || ! $user->exists) {
            return;
        }

        if ($this->hasher->needsRehash($user->getAuthPassword()) || $force) {
            $user->forceFill([
                'password' => $this->hasher->make($credentials['password']),
            ])->save();
        }
    }
}
