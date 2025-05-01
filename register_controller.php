<?php
require_once('Models/connectionDB.php');
require_once('Models/User.php');

$db = new connectionDB();
$conn = $db->connect();
$userModel = new User($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Convert role name to role ID
    switch (strtolower($role)) {
        case 'admin':
            $roleId = 1;
            break;
        case 'homeowner':
        case 'rental manager':
            $roleId = 2;
            break;
        case 'user':
        case 'customer':
        default:
            $roleId = 3;
            break;
    }

    // 1. Check if passwords match
    if ($password !== $confirm) {
        header("Location: Views/register.phtml?error=" . urlencode("Passwords do not match"));
        exit;
    }

    // 2. Password strength check (length, uppercase, number, special character)
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W]/', $password)
    ) {
        header("Location: Views/register.phtml?error=" . urlencode("Password must be 8+ chars, include uppercase, number & special char"));
        exit;
    }

    // 3. Email must be valid & @gmail.com
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
        header("Location: Views/register.phtml?error=" . urlencode("Only valid Gmail addresses allowed"));
        exit;
    }

    // 4. Check if email exists
    if ($userModel->emailExists($email)) {
        header("Location: Views/register.phtml?error=" . urlencode("Email already registered"));
        exit;
    }

    // 5. Check if username already exists
    if ($userModel->usernameExists($name)) {
        header("Location: Views/register.phtml?error=" . urlencode("Username already taken"));
        exit;
    }

    // 6. Check if username + password hash combo already exists
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    if ($userModel->isDuplicateUsernamePassword($name, $hashedPassword)) {
        header("Location: Views/register.phtml?error=" . urlencode("Username and password already used."));
        exit;
    }

    // 7. Register user
    if ($userModel->register($name, $email, $password, $roleId)) {
        header("Location: Views/login.phtml?success=" . urlencode("Registration successful"));
        exit;
    } else {
        header("Location: Views/register.phtml?error=" . urlencode("Registration failed"));
        exit;
    }
}
