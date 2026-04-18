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
     * Parse request body from JSON or fallback to $_POST.
     */
    protected function getPostData(): array {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $json = json_decode(file_get_contents('php://input'), true);
            return is_array($json) ? $json : [];
        }
        return $_POST;
    }

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

                // email
                if ($rule === 'email' && $value) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field] = 'Invalid email format.';
                        break;
                    }
                }

                // min:N
                if (strpos($rule, 'min:') === 0 && $value) {
                    $min = (int) substr($rule, 4);
                    if (mb_strlen($value) < $min) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least $min characters.";
                        break;
                    }
                }

                // max:N
                if (strpos($rule, 'max:') === 0 && $value) {
                    $max = (int) substr($rule, 4);
                    if (mb_strlen($value) > $max) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at most $max characters.";
                        break;
                    }
                }

                // in:a,b,c
                if (strpos($rule, 'in:') === 0 && $value) {
                    $allowed = explode(',', substr($rule, 3));
                    if (!in_array($value, $allowed)) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be one of: ' . implode(', ', $allowed) . '.';
                        break;
                    }
                }

                // phone (Vietnamese format)
                if ($rule === 'phone' && $value) {
                    if (!preg_match('/^(0|\+84)[0-9]{9}$/', $value)) {
                        $errors[$field] = 'Invalid phone number format.';
                        break;
                    }
                }

                // numeric
                if ($rule === 'numeric' && $value) {
                    if (!is_numeric($value)) {
                        $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be numeric.';
                        break;
                    }
                }

                // integer
                if ($rule === 'integer' && $value) {
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
