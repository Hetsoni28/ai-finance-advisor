<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\FamilyInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FamilyInviteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Public properties are automatically injected into the Blade view
    public FamilyInvite $invite;
    public string $acceptUrl;
    public string $inviterName;
    public string $workspaceName;

    /**
     * Create a new message instance.
     */
    public function __construct(FamilyInvite $invite, string $inviterName)
    {
        // Safely load the relationship
        $this->invite = $invite->loadMissing('family');
        $this->inviterName = $inviterName;
        $this->workspaceName = $this->invite->family->name ?? 'Secure Workspace';

        /*
        |--------------------------------------------------------------------------
        | 🔗 CLEAN CRYPTOGRAPHIC URL
        |--------------------------------------------------------------------------
        | We rely on our internal 64-char HMAC token and Database TTL.
        | No need for Signed URL query bloat which triggers spam filters.
        */
        $this->acceptUrl = route('user.families.accept', [
            'family' => $this->invite->family_id,
            'token'  => $this->invite->token,
        ]);
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->from(
                config('mail.from.address', 'hello@financeai.com'), 
                config('mail.from.name', 'FinanceAI Engine')
            )
            ->subject("[FinanceAI] Secure Access Provisioned: {$this->workspaceName}")
            ->view('emails.family_invite');
    }
}