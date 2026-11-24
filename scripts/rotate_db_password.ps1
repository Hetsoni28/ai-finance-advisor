# Updates the live database password in Vercel and redeploys.
#
# Use this right after resetting the avnadmin password in the Aiven console.
# The password is typed here (hidden) and sent straight to Vercel - it is never
# written to a file, printed, or saved in your shell history.
#
# Usage (from the project root):
#   powershell -ExecutionPolicy Bypass -File .\scripts\rotate_db_password.ps1

$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)

$secure = Read-Host 'Paste the NEW Aiven password (input is hidden)' -AsSecureString
$bstr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
try {
    $plain = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($bstr)
    if (-not $plain -or -not $plain.StartsWith('AVNS_')) {
        throw 'That does not look like an Aiven password (it should start with AVNS_). Nothing was changed.'
    }

    Write-Host "`nUpdating DB_PASSWORD in Vercel (production)..." -ForegroundColor Cyan
    $plain | vercel env add DB_PASSWORD production --force --sensitive | Out-Null
    if ($LASTEXITCODE -ne 0) { throw 'Vercel did not accept the new value. Check you are logged in with: vercel whoami' }
    Write-Host 'DB_PASSWORD updated.' -ForegroundColor Green
}
finally {
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($bstr)
    Remove-Variable plain -ErrorAction SilentlyContinue
}

Write-Host "`nRedeploying so the site uses the new password..." -ForegroundColor Cyan
& powershell -ExecutionPolicy Bypass -File .\deploy_vercel.ps1
