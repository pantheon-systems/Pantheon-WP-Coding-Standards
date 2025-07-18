#!/bin/bash

TEST_FILE=$1

if [ ! -f "$TEST_FILE" ]; then
    echo "Test file not found: $TEST_FILE"
    exit 1
fi

echo "Running test: $TEST_FILE"

# Use --basepath=. to ensure relative paths in the report.
JSON_REPORT=$(phpcs --standard=Pantheon-WP-Minimum/ruleset.xml --report=json --basepath=. --warning-severity=0 "$TEST_FILE" 2>/dev/null | sed -n '/{/,$p')

# Extract actual and expected errors.
ACTUAL_OUTPUT=$(echo "$JSON_REPORT" | jq -r '.files."'"$TEST_FILE"'".messages[]?.source' | sort)
EXPECTED_OUTPUT=$(sed -n 's/.*@expectedError //p' "$TEST_FILE" | sort)

echo "--------------------------------------------------"
echo "EXPECTED ERRORS:"
echo -e "${EXPECTED_OUTPUT:-<none>}"
echo "--------------------------------------------------"
echo "ACTUAL ERRORS:"
echo -e "${ACTUAL_OUTPUT:-<none>}"
echo "--------------------------------------------------"

# Compare the actual output with the expected output.
if ! diff <(echo "$ACTUAL_OUTPUT") <(echo "$EXPECTED_OUTPUT") > /dev/null; then
    echo "RESULT: FAILED ❌"
    echo "--- DIFF ---"
    diff <(echo "$ACTUAL_OUTPUT") <(echo "$EXPECTED_OUTPUT")
    exit 1
fi

echo "RESULT: PASSED ✅"
exit 0