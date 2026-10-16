# Quality Checks (Taskfile)

- Before considering a change done, run static analysis and the test suite through the Taskfile:
  - `task analyse` — PHPStan (`vendor/bin/sail bin phpstan analyse`, uses `phpstan-baseline.neon`).
  - `task test` — full Pest suite (`vendor/bin/sail pest --bail`; starts Sail first).
- While iterating, run only the affected tests: `vendor/bin/sail artisan test --compact <path> --filter=<name>`.
- Fix new PHPStan errors in the code; do not add them to the baseline. If PHPStan reports baseline entries as "was not matched" or with a wrong count (`ignore.unmatched`, `ignore.count`), remove or adjust those entries in `phpstan-baseline.neon`. Run `task analyse:baseline` only when the user asks.
- Without Docker/Sail (CI or cloud environments), use `task analyse:ci` and `task test:ci`.
- Known environment failures: the PDF tests (`EtichettePageTest`, `PatenteTest` "export into pdf") need `chrome-headless-shell`. Because `task test` uses `--bail`, it stops at the first one. Report them; don't try to fix them in the code.
