<?php

/**
 * BaseController — Base class for all API controllers.
 *
 * Response contract:
 *   Success: {"success": true, "data": ...}
 *   Error:   {"success": false, "message": "...", "errors": {...}}
 *
 * All responses set Content-Type: application/json and exit immediately.
 */
class BaseController {

    /**
     * Send a success JSON response.
     *
     * @param mixed $data   Data payload (array, object, or scalar)
     * @param int   $status HTTP status code (default 200)
     */
    protected function jsonResponse($data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'data' => $data]);
        exit();
    }

    /**
     * Send an error JSON response.
     *
     * @param string     $message Human-readable error message
     * @param int        $status  HTTP status code (default 400)
     * @param array|null $errors  Field-level validation errors (optional)
     */
    protected function jsonError(string $message, int $status = 400, $errors = null): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        $response = ['success' => false, 'message' => $message];
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        echo json_encode($response);
        exit();
    }

    /**
     * Parse and sanitize request body from JSON or fallback to $_POST.
     * Respects OCP: Knowledge of specific fields (like password) belongs to the caller.
     *
     * @param array $excludeFields Keys that should NOT be sanitized (e.g. ['password', 'content'])
     * @return array
     */
    protected function getPostData(array $excludeFields = []): array {
        $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
        $data = [];

        // Explicit Content-Type routing (Anti-Blind-Merging)
        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            $json = json_decode($input, true);
            $data = is_array($json) ? $json : [];
        } else {
            // Fallback to traditional $_POST
            $data = $_POST;
        }

        // Global XSS Sanitization (Recursive)
        $this->sanitizeRecursive($data, $excludeFields);

        return $data;
    }

    /**
     * Helper to recursively sanitize data using htmlspecialchars.
     *
     * @param array &$data          Reference to the data array
     * @param array $excludeFields  Keys that should NOT be sanitized
     */
    private function sanitizeRecursive(array &$data, array $excludeFields = []): void {
        foreach ($data as $key => &$value) {
            // Skip fields in the exclude list
            if (in_array($key, $excludeFields)) {
                continue;
            }

            if (is_array($value)) {
                $this->sanitizeRecursive($value, $excludeFields);
            } elseif (is_string($value)) {
                // Remove whitespace and escape HTML
                $value = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            }
        }
    }

    /**
     * Extracts only the specified fields from an array.
     * Use this to prevent 'Mass Assignment' vulnerabilities.
     *
     * Example: $safeData = $this->pick($postData, ['fullname', 'phone']);
     *
     * @param array $data   Source data
     * @param array $fields Allowed field keys
     * @return array        Filtered array
     */
    protected function pick(array $data, array $fields): array {
        $filtered = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $filtered[$field] = $data[$field];
            }
        }
        return $filtered;
    }

    /*
     * !!! BUSINESS LOGIC RULES RECAP !!!
     * 1. ZERO-TRUST PRICING: Never trust 'price' from client payload.
     *    Always query the DB for the authoritative base_price by ID.
     * 2. PRIVILEGE INTEGRITY: Never trust 'role', 'tier_id', or 'points' from client payload.
     *    These must be updated via specific administrative endpoints or server-side calculations.
     */

    /**
     * Validate data against rules. Returns empty array if all valid,
     * associative array of field => error message if invalid.
     *
     * Supported rules:
     *   required     — field must be present and non-empty
     *   email        — valid email format
     *   min:N        — minimum string length N
     *   max:N        — maximum string length N
     *   in:a,b,c     — value must be one of the listed values
     *   phone        — Vietnamese phone format
     *   numeric      — must be numeric
     *   integer      — must be integer
     *
     * @param array $data  Input data
     * @param array $rules Associative array: field => 'rule1|rule2|...'
     * @return array       Empty if valid, ['field' => 'error'] if invalid
     */
    protected function validate(array $data, array $rules): array {
        $errors = [];

        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;
            $ruleList = explode('|', $ruleset);

            foreach ($ruleList as $rule) {
                $rule = trim($rule);

                // required
                if ($rule === 'required') {
                    if ($value === null || $value === '') {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                        break; // Stop checking more rules for this field
                    }
                }

                // Skip further validation if value is empty and not required
                if (($value === null || $value === '') && $rule !== 'required') {
                    continue;
                }

                // Check if the field has an actual value (even if it's 0 or '0')
                $hasValue = ($value !== null && $value !== '');

                // email
                if ($rule === 'email' && $hasValue) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Invalid email format.';
                        break;
                    }
                }

                // min:N
                if (strpos($rule, 'min:') === 0 && $hasValue) {
                    $min = (int) substr($rule, 4);
                    if (is_numeric($value)) {
                        if ($value < $min) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least $min.";
                            break;
                        }
                    } else {
                        if (mb_strlen((string)$value) < $min) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least $min characters.";
                            break;
                        }
                    }
                }

                // max:N
                if (strpos($rule, 'max:') === 0 && $hasValue) {
                    $max = (int) substr($rule, 4);
                    if (is_numeric($value)) {
                        if ($value > $max) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at most $max.";
                            break;
                        }
                    } else {
                        if (mb_strlen((string)$value) > $max) {
                            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at most $max characters.";
                            break;
                        }
                    }
                }

                // in:a,b,c
                if (strpos($rule, 'in:') === 0 && $hasValue) {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowed)) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be one of: ' . implode(', ', $allowed) . '.';
                        break;
                    }
                }

                // phone (Vietnamese format)
                if ($rule === 'phone' && $hasValue) {
                    if (!is_string($value) || !preg_match('/^(0|\+84)[0-9]{9}$/', $value)) {
                        $errors[$field] = 'Invalid phone number format.';
                        break;
                    }
                }

                // numeric
                if ($rule === 'numeric' && $hasValue) {
                    if (!is_numeric($value)) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be numeric.';
                        break;
                    }
                }

                // integer
                if ($rule === 'integer' && $hasValue) {
                    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be an integer.';
                        break;
                    }
                }
            }
        }

        return $errors;
    }
}
