<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Family;
use App\Models\FamilyMember;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

class FamilyPolicy
{
    use HandlesAuthorization;

    /**
     * ========================================================================
     * 🛡️ THE MASTER OVERRIDE
     * Intercepts all policy checks. If the user is a System Admin, they 
     * bypass the pivot table checks entirely (useful for customer support).
     * ========================================================================
     */
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        return null; // Fall through to standard node checks
    }

    /*
    |--------------------------------------------------------------------------
    | View Family Dashboard
    |--------------------------------------------------------------------------
    */
    public function view(User $user, Family $family): bool
    {
        return $this->getMemberRole($user, $family) !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Invite Members (Owner/Admin only)
    |--------------------------------------------------------------------------
    */
    public function invite(User $user, Family $family): bool
    {
        $role = $this->getMemberRole($user, $family);
        
        return in_array($role, [
            strtolower(FamilyMember::ROLE_OWNER ?? 'owner'),
            strtolower(FamilyMember::ROLE_ADMIN ?? 'admin')
        ], true);
    }

    /*
    |--------------------------------------------------------------------------
    | Update / Modify Family Settings (Owner only)
    |--------------------------------------------------------------------------
    */
    public function update(User $user, Family $family): bool
    {
        // Failsafe: The absolute creator is ALWAYS the owner, even if pivot data corrupts
        if ($family->created_by === $user->id) {
            return true;
        }

        return $this->getMemberRole($user, $family) === strtolower(FamilyMember::ROLE_OWNER ?? 'owner');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Family (Owner only)
    |--------------------------------------------------------------------------
    */
    public function delete(User $user, Family $family): bool
    {
        // Only the absolute owner can destroy the hub
        return $this->update($user, $family);
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Node / Kick Member (Owner only)
    |--------------------------------------------------------------------------
    */
    public function removeMember(User $user, Family $family): bool
    {
        // Tied to update logic: Only owners can revoke access
        return $this->update($user, $family);
    }

    /*
    |--------------------------------------------------------------------------
    | 🧠 INTERNAL ROLE RESOLVER (N+1 PREVENTION ENGINE)
    |--------------------------------------------------------------------------
    | Retrieves the user's role in the family. Checks loaded relations first
    | to prevent database crashes during UI grid rendering.
    */
    private function getMemberRole(User $user, Family $family): ?string
    {
        // 1. Check if the relation is already eager-loaded on the User model
        if ($user->relationLoaded('families')) {
            $familyPivot = $user->families->firstWhere('id', $family->id);
            return $familyPivot ? strtolower($familyPivot->pivot->role) : null;
        }

        // 2. Check if the relation is already eager-loaded on the Family model
        // Note: Accommodates both standard 'users' and custom 'members' relationship names
        if ($family->relationLoaded('users')) {
            $userPivot = $family->users->firstWhere('id', $user->id);
            return $userPivot ? strtolower($userPivot->pivot->role) : null;
        }

        if ($family->relationLoaded('members')) {
            $memberPivot = $family->members->firstWhere('user_id', $user->id);
            return $memberPivot ? strtolower($memberPivot->role ?? $memberPivot->pivot->role) : null;
        }

        // 3. Fallback to direct, optimized DB query (Bypasses booting Eloquent models)
        // Note: Assumes standard pivot table naming. Adjust 'family_user' if your table is named differently.
        $pivotTable = 'family_user'; 
        if (class_exists(FamilyMember::class)) {
            $pivotTable = (new FamilyMember)->getTable();
        }

        $role = DB::table($pivotTable)
            ->where('family_id', $family->id)
            ->where('user_id', $user->id)
            ->value('role');

        return $role ? strtolower((string)$role) : null;
    }
}