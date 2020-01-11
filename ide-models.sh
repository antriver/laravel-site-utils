#!/bin/bash
./test-laravel-app/artisan ide-helper:models --reset --write --dir="../src" \
  --ignore="Antriver\LaravelSiteScaffolding\Models\Base\Pivot"
