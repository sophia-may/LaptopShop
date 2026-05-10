<?php

/**
 * BaseController — Base class for all API/AJAX controllers.
 * Ported from /backend. Provides:
 * - JSON response helpers
 * - XSS auto-sanitization
 * - Mass-assignment protection (pick)
 * - Declarative validation engine
 */
class BaseController {

    /**
     * Send a success JSON response.
     */
    protected function jsonResponse($data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'data' => $data]);
        exit();
    }

    /**
     * Send an error JSON response.
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
     * Parse and sanitize request body from JSON or $_POST.
     * Auto-sanitizes all fields except those in $excludeFields.
     */
    protected function getPostData(array $excludeFields = []): array {
        $contentType = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
        $data = [];

        if (strpos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            $json = json_decode($input, true);
            $data = is_array($json) ? $json : [];
        } else {
            $data = $_POST;
        }

        // Global XSS Sanitization (Recursive)
        $this->sanitizeRecursive($data, $excludeFields);

        return $data;
    }

    /**
     * Recursively sanitize data using htmlspecialchars.
     */
    private function sanitizeRecursive(array &$data, array $excludeFields = []): void {
        foreach ($data as $key => &$value) {
            if (in_array($key, $excludeFields)) {
                continue;
            }
            if (is_array($value)) {
                $this->sanitizeRecursive($value, $excludeFields);
            } elseif (is_string($value)) {
                $value = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            }
        }
    }

    /**
     * Mass-assignment protection: extract only allowed fields.
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

    /**
     * Declarative validation engine.
     * Supported rules: required, email, min:N, max:N, in:a,b,c, phone, numeric, integer
     *
     * @param array $data  Input data
     * @param array $rules ['field' => 'rule1|rule2|...']
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
                        break;
                    }
                }

                // Skip further validation if value is empty and not required
                if (($value === null || $value === '') && $rule !== 'required') {
                    continue;
                }

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
