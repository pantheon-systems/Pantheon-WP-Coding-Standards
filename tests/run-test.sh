#!/bin/bash
set -e

RULESET=$1
TEST_FILE=$2

if [ -z "$RULESET" ]; then
    echo "Usage: $0 <ruleset> <test_file>"
    exit 1
fi

if [ ! -f "$TEST_FILE" ]; then
    echo "Test file not found: $TEST_FILE"
    exit 1
fi

# Get the test name from the file header.
TEST_NAME=$(sed -n '3s/ \* //p' "$TEST_FILE")

echo ""
echo "Running test: ${TEST_NAME} with ruleset: $RULESET"
echo "$TEST_FILE"

# Use --basepath=. to ensure relative paths in the report.
JSON_REPORT=$(phpcs --standard="./$RULESET/ruleset.xml" --report=json --basepath=. "$TEST_FILE" 2>/dev/null | sed -n '/{/,$p')

# Debug
# echo "JSON Report: ${JSON_REPORT}"

# Extract actual and expected errors, including severity.
ACTUAL_OUTPUT=$(echo "$JSON_REPORT" | jq -r '.files."'"$TEST_FILE"'".messages[] | "[\(.type | ascii_upcase)] \(.source)"' | sort)
EXPECTED_OUTPUT=$( (sed -n "s/.*@expectedError\[$RULESET\] /\[ERROR\] /p" "$TEST_FILE"; sed -n "s/.*@expectedWarning\[$RULESET\] /\[WARNING\] /p" "$TEST_FILE") | sort)

echo "--------------------------------------------------"
echo "EXPECTED ISSUES:"
echo -e "${EXPECTED_OUTPUT:-<none>}"
echo "--------------------------------------------------"
echo "ACTUAL ISSUES:"
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
echo " "

exit 0