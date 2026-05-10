<?php
session_start();
require_once '../models/SettingModel.php';

header('Content-Type: application/json');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$settingModel = new SettingModel();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            $group = $_GET['group'] ?? null;
            $settings = $settingModel->getAll($group);
            echo json_encode(['success' => true, 'data' => $settings]);
            break;
            
        case 'save':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['settings']) || !is_array($data['settings'])) {
                throw new Exception('Invalid data format');
            }
            
            $result = $settingModel->updateMultiple($data['settings']);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Saved successfully!']);
            } else {
                throw new Exception('Error saving settings');
            }
            break;
            
        case 'get_by_group':
            $group = $_GET['group'] ?? null;
            
            if (!$group) {
                throw new Exception('Group parameter is required');
            }
            
            $settings = $settingModel->getByGroup($group);
            echo json_encode(['success' => true, 'data' => $settings]);
            break;
            
        case 'set':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['key']) || !isset($data['value'])) {
                throw new Exception('Key and value are required');
            }
            
            $key = $data['key'];
            $value = $data['value'];
            
            $result = $settingModel->set($key, $value);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Saved successfully!']);
            } else {
                throw new Exception('Error saving settings');
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
