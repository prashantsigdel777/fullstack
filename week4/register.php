<?php
// -------------------- INITIAL SETUP --------------------
$name = $email = '';
$errors = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
    'general' => ''
];
$success_message = '';

// -------------------- WHEN FORM IS SUBMITTED --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form values
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // --------- VALIDATION ---------
    // Name
    if ($name === '') {
        $errors['name'] = 'Name is required.';
    }

    // Email
    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email.';
    }

    // Password
    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }

    // Confirm Password
    if ($confirm_password === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    // Check if there are any errors
    $has_errors = false;
    foreach ($errors as $field => $msg) {
        if ($field === 'general') continue;
        if (!empty($msg)) {
            $has_errors = true;
            break;
        }
    }

    // --------- IF NO ERRORS, SAVE TO JSON ---------
    if (!$has_errors) {
        $json_file = __DIR__ . '/users.json';

        // If file doesn't exist, try to create it with empty array
        if (!file_exists($json_file)) {
            if (file_put_contents($json_file, json_encode([])) === false) {
                $errors['general'] = 'Could not create users.json file.';
            }
        }

        if ($errors['general'] === '') {
            // Read existing users
            $json_data = @file_get_contents($json_file);
            if ($json_data === false) {
                $errors['general'] = 'Could not read users.json file.';
            } else {
                $users = json_decode($json_data, true);
                if (!is_array($users)) {
                    $users = [];
                }

                // Optional: Check if email already exists
                foreach ($users as $user) {
                    if (isset($user['email']) && strtolower($user['email']) === strtolower($email)) {
                        $errors['email'] = 'This email is already registered.';
                        $has_errors = true;
                        break;
                    }
                }

                if (!$has_errors) {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // New user data
                    $new_user = [
                        'name'  => $name,
                        'email' => $email,
                        'password' => $hashed_password
                    ];

                    // Add to array
                    $users[] = $new_user;

                    // Save back to JSON
                    $new_json_data = json_encode($users, JSON_PRETTY_PRINT);
                    if (file_put_contents($json_file, $new_json_data) === false) {
                        $errors['general'] = 'Could not save user data.';
                    } else {
                        $success_message = 'Registration successful!';
                        // Clear form fields
                        $name = $email = '';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>

    <!-- LINK TO EXTERNAL CSS FILE -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>User Registration</h1>

    <?php if ($success_message): ?>
        <div class="alert success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($errors['general']): ?>
        <div class="alert error-alert">
            <?php echo htmlspecialchars($errors['general']); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" novalidate class="form">

        <div class="field">
            <label for="name">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($name); ?>"
                required
            >
            <?php if ($errors['name']): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['name']); ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email); ?>"
                required
            >
            <?php if ($errors['email']): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['email']); ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >
            <?php if ($errors['password']): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['password']); ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
            >
            <?php if ($errors['confirm_password']): ?>
                <div class="error-text"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn">Register</button>

    </form>
</div>

</body>
</html>
