#!/bin/bash
cd sin || exit

./vendor/bin/sail  up -d
./vendor/bin/sail  test
