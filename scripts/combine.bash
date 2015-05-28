#!/bin/bash
dir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cat $dir/std-js/deprefixer.js $dir/std-js/portotypes.js $dir/std-js/poly_modern.js $dir/std-js/support_test.js $dir/std-js/functions.js $dir/std-js/zq.js $dir/std-js/json_response.js $dir/custom.js > $dir/combined.js
