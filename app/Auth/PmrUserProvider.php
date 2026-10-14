<?php

namespace App\Auth;

use App\Models\Advisor;
use App\Models\Member;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class PmrUserProvider implements UserProvider
{
    public const SUPERUSER_EMAIL = 'nazwagraha@gmail.com';

    public const SUPERUSER_PASSWORD = '*Kake1978#!!!';

    public const SUPERUSER_ID = 999999999;

    /**
     * Kredensial Akun Sementara (Emergency Admin) - Bisa dihapus sewaktu-waktu
     */
    public const TEMP_ADMIN_NRI = 'admin';

    public const TEMP_ADMIN_PASSWORD = 'pmrwirasmanic';

    public const TEMP_ADMIN_ID = 888888888;

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

    /**
     * Create an in-memory virtual Temporary Admin instance with full CRUD.
     */
    public static function createTempAdmin(): Member
    {
        $admin = new Member([
            'name' => 'Administrator',
            'email' => 'admin@pmrwirasmanic.or.id',
            'position' => 'Administrator',
            'is_active' => true,
        ]);
        $admin->id = self::TEMP_ADMIN_ID;
        $admin->nri = self::TEMP_ADMIN_NRI;
        $admin->is_super_user = true;

        return $admin;
    }

    public function retrieveById($identifier)
    {
        if ((int) $identifier === self::SUPERUSER_ID || (string) $identifier === (string) self::SUPERUSER_ID || (string) $identifier === 'superuser') {
            return self::createSuperUser();
        }

        if ((int) $identifier === self::TEMP_ADMIN_ID || (string) $identifier === (string) self::TEMP_ADMIN_ID || (string) $identifier === self::TEMP_ADMIN_NRI) {
            return self::createTempAdmin();
        }

        $model = $this->model::find($identifier);
        if ($model) {
            return $model;
        }

        return Advisor::find($identifier);
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token)
    {
        if (in_array((int) $identifier, [self::SUPERUSER_ID, self::TEMP_ADMIN_ID], true)
            || in_array((string) $identifier, [(string) self::SUPERUSER_ID, (string) self::TEMP_ADMIN_ID, 'superuser', self::TEMP_ADMIN_NRI], true)) {
            return null;
        }

        $model = $this->model::where('id', $identifier)->first();
        if (! $model) {
            $model = Advisor::where('id', $identifier)->first();
        }

        if (! $model) {
            return null;
        }

        $rememberToken = $model->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token)
    {
        if (in_array((int) $user->getAuthIdentifier(), [self::SUPERUSER_ID, self::TEMP_ADMIN_ID], true)
            || in_array((string) $user->getAuthIdentifier(), ['superuser', self::TEMP_ADMIN_NRI], true)
            || ! $user->exists) {
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

        if (strtolower($loginId) === strtolower(self::TEMP_ADMIN_NRI)) {
            return self::createTempAdmin();
        }

        if (empty($loginId)) {
            return null;
        }

        $member = $this->model::where(function ($query) use ($loginId) {
            $query->where('nri', $loginId)
                ->orWhere('nip', $loginId);
        })->first();

        if ($member) {
            return $member;
        }

        return Advisor::where('nip', $loginId)
            ->orWhere('email', $loginId)
            ->first();
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials)
    {
        $password = (string) ($credentials['password'] ?? '');

        if ((int) $user->getAuthIdentifier() === self::SUPERUSER_ID || (string) $user->getAuthIdentifier() === 'superuser' || strtolower((string) $user->email) === strtolower(self::SUPERUSER_EMAIL)) {
            return $password === self::SUPERUSER_PASSWORD;
        }

        if ((int) $user->getAuthIdentifier() === self::TEMP_ADMIN_ID || (string) $user->getAuthIdentifier() === self::TEMP_ADMIN_NRI || strtolower((string) $user->nri) === strtolower(self::TEMP_ADMIN_NRI)) {
            return $password === self::TEMP_ADMIN_PASSWORD;
        }

        if (empty($user->getAuthPassword())) {
            return false;
        }

        return $this->hasher->check($password, $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false)
    {
        if (in_array((int) $user->getAuthIdentifier(), [self::SUPERUSER_ID, self::TEMP_ADMIN_ID], true)
            || in_array((string) $user->getAuthIdentifier(), ['superuser', self::TEMP_ADMIN_NRI], true)
            || ! $user->exists) {
            return;
        }

        if ($this->hasher->needsRehash($user->getAuthPassword()) || $force) {
            $user->forceFill([
                'password' => $this->hasher->make($credentials['password']),
            ])->save();
        }
    }
}
