#!/bin/bash
./test-laravel-app/artisan ide-helper:models --reset --write --dir="../src" \
  --ignore="Antriver\LaravelSiteUtils\Models\Base\Pivot"
