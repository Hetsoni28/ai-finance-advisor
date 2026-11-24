# Runs pending database migrations against the LIVE (production) database.
#
# Vercel deploys do not run migrations, so run this once after deploying a change
# that adds a migration. It asks for the connection details when you run it, so no
# password is stored in this file, in .env, or in your shell history.
#
# Usage (from the project root):
#   powershell -ExecutionPolicy Bypass -File .\scripts\migrate_production.ps1
#
# The values are in Aiven console -> your MySQL service -> Overview -> Connection information.

$ErrorActionPreference = 'Stop'
Set-Location (Split-Path $PSScriptRoot -Parent)

if (Test-Path 'bootstrap/cache/config.php') {
    Write-Host 'Config is cached; clearing it so the live settings are used.' -ForegroundColor Yellow
    php artisan config:clear | Out-Null
}

$dbHost = Read-Host 'Host (e.g. something.aivencloud.com)'
$port   = Read-Host 'Port'
$name   = Read-Host 'Database name (usually defaultdb)'
$user   = Read-Host 'User (usually avnadmin)'
$secure = Read-Host 'Password' -AsSecureString
$ca     = Read-Host 'Path to Aiven ca.pem (press Enter to skip)'

$bstr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
try {
    $env:DB_CONNECTION = 'mysql'
    $env:DB_HOST       = $dbHost
    $env:DB_PORT       = $port
    $env:DB_DATABASE   = $name
    $env:DB_USERNAME   = $user
    $env:DB_PASSWORD   = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($bstr)
    if ($ca) { $env:MYSQL_ATTR_SSL_CA = (Resolve-Path $ca).Path }

    Write-Host "`nMigrations on the live database:" -ForegroundColor Cyan
    php artisan migrate:status
    if ($LASTEXITCODE -ne 0) { throw 'Could not connect. Check the details (and the ca.pem path) and try again.' }

    $answer = Read-Host "`nRun the pending ones (marked 'No') now? Type yes to continue"
    if ($answer -ne 'yes') { Write-Host 'Nothing changed.'; return }

    php artisan migrate --force
    if ($LASTEXITCODE -ne 0) { throw 'Migration failed - see the error above.' }
    Write-Host "`nDone. The live database is up to date." -ForegroundColor Green
}
finally {
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($bstr)
    foreach ($k in 'DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME','DB_PASSWORD','MYSQL_ATTR_SSL_CA') {
        Remove-Item "Env:$k" -ErrorAction SilentlyContinue
    }
}
