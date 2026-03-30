<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Family;
use App\Models\FamilyInvite;
use App\Models\User;
use App\Mail\FamilyInviteMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * 🚀 FinanceAI Secure Transmission Engine
 * Handles cryptographic access tokens and SMTP dispatching for workspace nodes.
 */
class FamilyInviteService
{
    /**
     * Executes the secure invitation protocol.
     *
     * @param Family $family
     * @param string $email
     * @return FamilyInvite
     * @throws ValidationException
     */
    public function sendEmailInvite(Family $family, string $email): FamilyInvite
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user, 403, 'Unauthenticated Node.');

        // 1. Verify IAM Permissions (Admin/Owner only)
        $this->verifyNodeAuthority($family, $user);

        $email = strtolower(trim($email));

        /*
        |--------------------------------------------------------------------------
        | 🛡️ RULE 1: PREVENT SELF-REPLICATION
        |--------------------------------------------------------------------------
        */
        if ($user->email === $email) {
            throw ValidationException::withMessages([
                'email' => 'Protocol Violation: You cannot transmit an access token to your own node.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 🛡️ RULE 2: PREVENT DUPLICATE ACCESS
        |--------------------------------------------------------------------------
        */
        $isAlreadyMember = DB::table('family_user')
            ->join('users', 'family_user.user_id', '=', 'users.id')
            ->where('family_user.family_id', $family->id)
            ->where('users.email', $email)
            ->exists();

        if ($isAlreadyMember) {
            throw ValidationException::withMessages([
                'email' => 'Target identity is already an authorized node in this workspace.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 🛡️ RULE 3: ANTI-SPAM CRYPTOGRAPHIC COOLDOWN (CRITICAL FIX)
        | Prevents SMTP quota exhaustion by locking the target email for 5 minutes.
        |--------------------------------------------------------------------------
        */
        $cooldownKey = "invite_lock_{$family->id}_" . md5($email);
        
        if (Cache::has($cooldownKey)) {
            throw ValidationException::withMessages([
                'email' => 'Transmission in progress. Please wait 5 minutes before pinging this address again.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 🛡️ RULE 4: GLOBAL RATE LIMITING
        | Restricts total outbound invites per user to 10 per hour.
        |--------------------------------------------------------------------------
        */
        $hourlyRateKey = "invite_rate_{$user->id}";
        $hourlyAttempts = Cache::get($hourlyRateKey, 0);

        if ($hourlyAttempts >= 10) {
            throw ValidationException::withMessages([
                'email' => 'System limits reached. You may only dispatch 10 invites per hour.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ⚙️ EXECUTE SECURE TRANSACTION
        |--------------------------------------------------------------------------
        */
        $invite = DB::transaction(function () use ($family, $email, $user) {
            
            // Purge any dead tokens specifically for this target to prevent collisions
            FamilyInvite::where('family_id', $family->id)
                ->where('email', $email)
                ->where('expires_at', '<', now())
                ->delete();

            $activeInvite = FamilyInvite::where('family_id', $family->id)
                ->where('email', $email)
                ->first();

            if ($activeInvite) {
                // Refresh the TTL (Time-To-Live) on existing tokens
                $activeInvite->update([
                    'expires_at' => now()->addDays(7), // Standard enterprise 7-day TTL
                ]);
                return $activeInvite;
            }

            // Generate a mathematically unforgeable HMAC token
            $rawToken = Str::random(64);
            $secureToken = hash_hmac('sha256', $rawToken, config('app.key'));

            return FamilyInvite::create([
                'family_id'  => $family->id,
                'email'      => $email,
                'token'      => $secureToken,
                'expires_at' => now()->addDays(7),
                'created_by' => $user->id,
            ]);
        });

        /*
        |--------------------------------------------------------------------------
        | 📧 DISPATCH ASYNCHRONOUS PAYLOAD
        |--------------------------------------------------------------------------
        */
        try {
            Mail::to($email)->queue(
                new FamilyInviteMail($invite, $user->name ?? 'A Workspace Admin')
            );

            // Lock the email target and increment the hourly limit upon successful queue
            Cache::put($cooldownKey, true, now()->addMinutes(5));
            Cache::put($hourlyRateKey, $hourlyAttempts + 1, now()->addHour());

            // Invalidate frontend cache
            Cache::forget("family_dashboard_{$family->id}");

        } catch (Throwable $e) {
            // If the mail server is down, we must wipe the cooldown so they can try again later
            Cache::forget($cooldownKey);
            throw ValidationException::withMessages([
                'email' => 'SMTP Dispatch Failed: Unable to reach target mail server.',
            ]);
        }

        return $invite;
    }

    /**
     * 🔐 STRICT IDENTITY & ACCESS MANAGEMENT (IAM) CHECK
     * Bypasses Eloquent model booting for raw, lightning-fast database verification.
     */
    private function verifyNodeAuthority(Family $family, User $user): void
    {
        // Check standard pivot table structure safely
        $role = DB::table('family_user')
            ->where('family_id', $family->id)
            ->where('user_id', $user->id)
            ->value('role');

        if (!in_array(strtolower((string)$role), ['owner', 'admin'], true)) {
            abort(403, 'Unauthorized. Escalated privileges required to transmit invites.');
        }
    }
}