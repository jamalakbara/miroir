# Copy existing Windows-installed vendor plugins into the Linux plugin volume.
# Existing destinations are preserved, so this never rolls back container updates.
$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$pluginSource = Join-Path $projectRoot 'wp-content/plugins'
$transferDirectory = Join-Path ([IO.Path]::GetTempPath()) ('miroir-plugins-' + [guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Path $transferDirectory | Out-Null
$archive = Join-Path $transferDirectory 'plugins.tar'
Push-Location $projectRoot
try {
    $vendorEntries = @(Get-ChildItem -LiteralPath $pluginSource -Force | Where-Object { $_.Name -notlike 'miroir-*' } | ForEach-Object Name)
    if ($vendorEntries.Count -eq 0) { Write-Host 'No Windows vendor plugins to import.'; return }
    # Archive on Windows first: one large file crosses the mount instead of thousands.
    tar.exe -cf $archive -C $pluginSource @vendorEntries
    if ($LASTEXITCODE -ne 0) { throw 'Creating the plugin archive failed.' }
    $copyScript = @'
set -eu
staging=$(mktemp -d /var/www/html/wp-content/plugins/.import-XXXXXX)
trap 'rm -rf -- "$staging"' EXIT
tar -xf /seed/plugins.tar -C "$staging"
chown -R 33:33 "$staging"
for source in "$staging"/*; do
    [ -e "$source" ] || continue
    name=${source##*/}
    case "$name" in miroir-*) continue ;; esac
    destination="/var/www/html/wp-content/plugins/$name"
    if [ -e "$destination" ]; then
        echo "Preserving existing $name"
    else
        echo "Copying $name to Linux storage"
        mv "$source" "$destination"
    fi
done
echo "Import complete; preserved existing plugin versions."
'@
    # Base64 preserves LF line endings across PowerShell's native-command boundary.
    $encodedScript = [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes($copyScript.Replace("`r", '') + "`n"))
    docker compose run --rm --no-deps -T --user root --volume "${transferDirectory}:/seed:ro" --entrypoint sh wpcli -c "echo $encodedScript | base64 -d | sh"
    if ($LASTEXITCODE -ne 0) { throw 'Plugin volume initialization failed.' }
} finally {
    Pop-Location
    if (Test-Path -LiteralPath $archive) { Remove-Item -LiteralPath $archive }
    Remove-Item -LiteralPath $transferDirectory
}
