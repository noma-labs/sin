# Quality Checks (Composer scripts)

- Before considering a change done, run linting, static analysis and the test suite through the Composer scripts in `composer.json`:
  - `vendor/bin/sail composer lint` — formats and refactors the code (Pint, Prettier `format:write`, Rector). Use `vendor/bin/sail composer lint:check` to only check without writing.
  - `vendor/bin/sail composer analyse` — PHPStan (uses `phpstan-baseline.neon`).
  - `vendor/bin/sail composer test -- --bail` — full Pest suite, stopping at the first failure (Sail must be running: `vendor/bin/sail up -d`).
  - `vendor/bin/sail composer qa` — `lint:check`, `analyse` and `test` together.
- While iterating, run only the affected tests: `vendor/bin/sail artisan test --compact <path> --filter=<name>`.
- Fix new PHPStan errors in the code; do not add them to the baseline. If PHPStan reports baseline entries as "was not matched" or with a wrong count (`ignore.unmatched`, `ignore.count`), remove or adjust those entries in `phpstan-baseline.neon`. Run `vendor/bin/sail composer analyse:baseline` only when the user asks.
- Without Docker/Sail (CI or cloud environments), run the same scripts with plain `composer`: `composer lint:check`, `composer analyse` and `composer test`.
- Known environment failures: the PDF tests (`EtichettePageTest`, `PatenteTest` "export into pdf") need `chrome-headless-shell`. With `--bail` the suite stops at the first one. Report them; don't try to fix them in the code.
