#Requires -RunAsAdministrator
<#
.SYNOPSIS
    Automatically installs MongoDB PHP Extension for XAMPP on Windows
.DESCRIPTION
    Detects PHP version, downloads correct mongodb DLL, installs it, and configures php.ini
#>

$ErrorActionPreference = "Stop"
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  MongoDB PHP Extension Auto-Installer" -ForegroundColor Cyan
Write-Host "  For XAMPP on Windows" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Find PHP
$phpPaths = @(
    "C:\xampp\php\php.exe",
    "C:\xampp2\php\php.exe",
    "C:\wamp64\bin\php\php.exe",
    "$env:LOCALAPPDATA\laragon\bin\php\php.exe"
)

$phpExe = $null
foreach ($path in $phpPaths) {
    if (Test-Path $path) {
        $phpExe = $path
        break
    }
}

if (-not $phpExe) {
    # Try to find php.exe in common locations
    $found = Get-ChildItem -Path "C:\xampp" -Name "php.exe" -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($found) {
        $phpExe = Join-Path "C:\xampp" $found
    }
}

if (-not $phpExe -or -not (Test-Path $phpExe)) {
    Write-Host "ERROR: Could not find php.exe!" -ForegroundColor Red
    Write-Host "Please make sure XAMPP is installed at C:\xampp\" -ForegroundColor Yellow
    Write-Host "Or set the path manually in this script." -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit 1
}

$phpDir = Split-Path -Parent $phpExe
$extDir = Join-Path $phpDir "ext"
$iniFile = Join-Path $phpDir "php.ini"

Write-Host "[OK] PHP found: $phpExe" -ForegroundColor Green
Write-Host "[OK] Extensions dir: $extDir" -ForegroundColor Green
Write-Host "[OK] php.ini: $iniFile" -ForegroundColor Green
Write-Host ""

# Step 2: Detect PHP version and architecture
Write-Host "Detecting PHP configuration..." -ForegroundColor Yellow
$phpInfo = & $phpExe -r "echo PHP_VERSION . '|' . (PHP_INT_SIZE*8) . '|' . (ZEND_THREAD_SAFE ? 'ts' : 'nts') . '|' . ini_get('extension_dir');"
$parts = $phpInfo -split "\|"
$phpVersion = $parts[0]
$phpArch = $parts[1]
$phpTs = $parts[2]
$phpExtDir = $parts[3]

# Extract major.minor version
$versionMatch = [regex]::Match($phpVersion, "^(\d+\.\d+)")
$phpShortVersion = $versionMatch.Groups[1].Value

Write-Host "  PHP Version: $phpVersion (using $phpShortVersion)" -ForegroundColor White
Write-Host "  Architecture: $phpArch-bit" -ForegroundColor White
Write-Host "  Thread Safe: $phpTs" -ForegroundColor White
Write-Host ""

# Step 3: Check if already installed
$alreadyInstalled = & $phpExe -r "echo extension_loaded('mongodb') ? 'YES' : 'NO';"
if ($alreadyInstalled -eq "YES") {
    $existingVersion = & $phpExe -r "echo phpversion('mongodb');"
    Write-Host "[INFO] MongoDB extension is already installed!" -ForegroundColor Green
    Write-Host "  Version: $existingVersion" -ForegroundColor Green
    Write-Host ""
    $continue = Read-Host "Do you want to reinstall/upgrade? (y/N)"
    if ($continue -ne "y" -and $continue -ne "Y") {
        Write-Host "Skipping installation. Running composer..." -ForegroundColor Cyan
        goto ComposerStep
    }
}

# Step 4: Download MongoDB DLL
$dllName = "php_mongodb.dll"
$dllPath = Join-Path $extDir $dllName

# Build download URL for pecl
$tsUpper = $phpTs.ToUpper()
$downloadUrl = "https://pecl.php.net/get/mongodb/stable/windows/$phpShortVersion/$tsUpper-$phpArch"

# Alternative: Use the PECL direct DLL download approach
# The PECL windows DLLs are at a specific pattern
$peclDllUrl = "https://downloads.php.net/~windows/pecl/releases/mongodb/1.17.0/php_mongodb-1.17.0-$phpShortVersion-$phpTs-$phpArch.dll"

Write-Host "Downloading MongoDB extension..." -ForegroundColor Yellow
Write-Host "  URL: $peclDllUrl" -ForegroundColor Gray

# Try to download
try {
    $tempDll = "$env:TEMP\$dllName"
    Invoke-WebRequest -Uri $peclDllUrl -OutFile $tempDll -UseBasicParsing
    Write-Host "[OK] Download successful!" -ForegroundColor Green
} catch {
    Write-Host "[WARN] Direct download failed. Trying alternative method..." -ForegroundColor Yellow

    # Try different versions
    $versions = @("1.17.0", "1.16.2", "1.16.1", "1.16.0", "1.15.0")
    $downloaded = $false
    foreach ($ver in $versions) {
        $url = "https://downloads.php.net/~windows/pecl/releases/mongodb/$ver/php_mongodb-$ver-$phpShortVersion-$phpTs-$phpArch.dll"
        try {
            Invoke-WebRequest -Uri $url -OutFile $tempDll -UseBasicParsing -ErrorAction Stop
            Write-Host "[OK] Downloaded version $ver" -ForegroundColor Green
            $downloaded = $true
            break
        } catch {
            continue
        }
    }

    if (-not $downloaded) {
        Write-Host "ERROR: Could not download MongoDB DLL automatically." -ForegroundColor Red
        Write-Host "Please download manually from: https://pecl.php.net/package/mongodb" -ForegroundColor Yellow
        Write-Host "Look for: php_mongodb-X.X.X-$phpShortVersion-$phpTs-$phpArch.dll" -ForegroundColor Yellow
        Write-Host "Then copy it to: $extDir" -ForegroundColor Yellow
        Read-Host "Press Enter to exit"
        exit 1
    }
}

# Step 5: Copy DLL to ext directory
Copy-Item -Path $tempDll -Destination $dllPath -Force
Write-Host "[OK] Installed: $dllPath" -ForegroundColor Green

# Step 6: Update php.ini
Write-Host "Updating php.ini..." -ForegroundColor Yellow
$iniContent = Get-Content $iniFile -Raw -ErrorAction SilentlyContinue

if (-not $iniContent) {
    Write-Host "ERROR: Could not read php.ini" -ForegroundColor Red
    exit 1
}

# Check if extension already exists
if ($iniContent -match "extension=mongodb") {
    Write-Host "[OK] extension=mongodb already in php.ini" -ForegroundColor Green
} else {
    # Add extension line after other extension entries
    $iniContent = $iniContent -replace "(extension=openssl", "extension=mongodb`r`nextension=openssl"
    # If openssl not found, add at end of Dynamic Extensions section
    if ($iniContent -notmatch "extension=mongodb") {
        $iniContent = $iniContent -replace "(;extension=xsl", "extension=mongodb`r`n;extension=xsl"
    }
    # Final fallback - append to file
    if ($iniContent -notmatch "extension=mongodb") {
        $iniContent += "`r`n; MongoDB Extension`r`nextension=mongodb`r`n"
    }
    Set-Content -Path $iniFile -Value $iniContent -NoNewline
    Write-Host "[OK] Added extension=mongodb to php.ini" -ForegroundColor Green
}

# Step 7: Also update apache's php.ini if different
$apacheIni = "C:\xampp\apache\bin\php.ini"
if (Test-Path $apacheIni) {
    $apacheIniContent = Get-Content $apacheIni -Raw -ErrorAction SilentlyContinue
    if ($apacheIniContent -and ($apacheIniContent -notmatch "extension=mongodb")) {
        $apacheIniContent = $apacheIniContent -replace "(extension=openssl", "extension=mongodb`r`nextension=openssl"
        if ($apacheIniContent -notmatch "extension=mongodb") {
            $apacheIniContent += "`r`n; MongoDB Extension`r`nextension=mongodb`r`n"
        }
        Set-Content -Path $apacheIni -Value $apacheIniContent -NoNewline
        Write-Host "[OK] Updated Apache php.ini too" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  MongoDB Extension Installed!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "IMPORTANT: Please RESTART Apache from XAMPP Control Panel!" -ForegroundColor Magenta
Write-Host ""

:ComposerStep
# Step 8: Run composer
$composerPath = Join-Path $phpDir "composer.phar"
$composerGlobal = (Get-Command composer -ErrorAction SilentlyContinue).Source

Write-Host "Installing MongoDB PHP Library via Composer..." -ForegroundColor Yellow

Set-Location -Path $PSScriptRoot

if ($composerGlobal) {
    Write-Host "  Using global composer: $composerGlobal" -ForegroundColor Gray
    & composer require mongodb/mongodb --no-interaction
} elseif (Test-Path $composerPath) {
    Write-Host "  Using: $composerPath" -ForegroundColor Gray
    & $phpExe $composerPath require mongodb/mongodb --no-interaction
} else {
    Write-Host "Composer not found. Please install Composer first from https://getcomposer.org/download/" -ForegroundColor Yellow
    Write-Host "Or run these commands manually:" -ForegroundColor Yellow
    Write-Host "  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"" -ForegroundColor Cyan
    Write-Host "  php composer-setup.php" -ForegroundColor Cyan
    Write-Host "  php -r "unlink('composer-setup.php');"" -ForegroundColor Cyan
    Write-Host "  php composer.phar require mongodb/mongodb" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Setup Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor White
Write-Host "  1. RESTART Apache from XAMPP Control Panel" -ForegroundColor Yellow
Write-Host "  2. Open http://localhost/pgi-bank-mongodb/pages/connection-test.php" -ForegroundColor Yellow
Write-Host "  3. Update includes/config.php with your MongoDB Atlas URI" -ForegroundColor Yellow
Write-Host ""
Read-Host "Press Enter to exit"
