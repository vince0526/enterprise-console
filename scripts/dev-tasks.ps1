<#
Convenience PowerShell task runner mirroring Makefile targets.
Usage examples:
  ./scripts/dev-tasks.ps1 install
  ./scripts/dev-tasks.ps1 qa-full
  ./scripts/dev-tasks.ps1 docs
#>
param(
  [Parameter(Position=0)][string]$Task = 'help'
)

function Step($m){ Write-Host "[TASK] $m" -ForegroundColor Cyan }

switch ($Task) {
  'help' {
    "Available tasks: install, dev, qa, qa-full, test, coverage, build, docs, clean, fresh, sail-up, sail-build, sail-test"; break }
  'install' { Step 'Install dependencies'; composer install --no-interaction; npm install; break }
  'dev' { Step 'Dev environment'; composer run dev; break }
  'qa' { Step 'Pint + PHPStan'; composer run check; break }
  'qa-full' { Step 'Full quality'; composer run check-all; break }
  'test' { Step 'Tests'; php artisan test; break }
  'coverage' { Step 'Coverage'; php -d xdebug.mode=coverage artisan test --coverage-text; break }
  'build' { Step 'Build assets'; npm run build; break }
  'docs' { Step 'Regenerate docs'; python scripts/env_setup_md_to_docx.py; python scripts/md_to_docx.py; break }
  'clean' { Step 'Clean'; Remove-Item -Recurse -Force vendor,node_modules -ErrorAction SilentlyContinue; break }
  'fresh' { Step 'Fresh migrate'; php artisan migrate:fresh --seed; break }
  'sail-up' { Step 'Sail up'; bash ./vendor/bin/sail up -d; break }
  'sail-build' { Step 'Sail build'; bash ./vendor/bin/sail build --no-cache; break }
  'sail-test' { Step 'Sail tests'; bash ./vendor/bin/sail php artisan test; break }
  Default { Write-Host "Unknown task: $Task" -ForegroundColor Red; exit 1 }
}
