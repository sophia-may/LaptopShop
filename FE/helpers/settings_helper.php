<?php
// Helper function to get settings from JSON file
// Use this in client-facing pages to load dynamic content

require_once __DIR__ . '/../models/SettingModel.php';

// Initialize setting model (global variable to reuse)
global $settingModel;
if (!isset($settingModel)) {
    $settingModel = new SettingModel();
}

/**
 * Get a single setting value
 * @param string $key The setting key (supports dot notation: "contact.phone")
 * @param mixed $default Default value if not found
 * @return string The setting value
 */
function getSetting($key, $default = '') {
    global $settingModel;
    
    // Cache settings in static variable for performance
    static $cache = [];
    
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    
    // Get from model
    $value = $settingModel->get($key, $default);
    
    // Cache it
    $cache[$key] = $value;
    
    return $value;
}

/**
 * Get all settings for a group
 * @param string $group The setting group name
 * @return array Associative array of settings
 */
function getSettings($group) {
    global $settingModel;
    
    // Cache key for this group
    static $cache = [];
    
    if (isset($cache[$group])) {
        return $cache[$group];
    }
    
    // Get from model
    $settings = $settingModel->getByGroup($group);
    
    // Cache it
    $cache[$group] = $settings;
    
    return $settings;
}

/**
 * Clear settings cache
 * Not needed anymore since we use static variables instead of session
 */
function clearSettingsCache() {
    // Static variables are cleared on each request, no action needed
}

/**
 * Get image URL with cache busting parameter
 * Automatically adds timestamp to prevent browser caching issues
 * @param string $imagePath The image path from settings
 * @return string Image URL with cache busting parameter
 */
function getImageUrl($imagePath) {
    if (empty($imagePath)) {
        return '';
    }
    
    // Check if file exists
    if (file_exists($imagePath)) {
        $timestamp = filemtime($imagePath);
        return $imagePath . '?v=' . $timestamp;
    }
    
    // If file doesn't exist, return with default version
    return $imagePath . '?v=1';
}
