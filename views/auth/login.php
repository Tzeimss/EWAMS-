<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .auth-container { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); padding: 20px; }
        .auth-card { background: #ffffff; border-radius: 12px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); width: 100%; max-width: 420px; overflow: hidden; }
        .auth-header { background: #1e3a5f; color: white; padding: 30px; text-align: center; }
        .auth-header h1 { font-size: 24px; font-weight: 600; margin-bottom: 8px; }
        .auth-header p { opacity: 0.85; font-size: 14px; }
        .auth-body { padding: 30px; }
        .input-group { position: relative; display: flex; align-items: center; }
        .input-group i { position: absolute; left: 14px; color: #718096; }
        .input-group input { padding-left: 40px; }
        .auth-footer { padding: 20px; text-align: center; background: #f5f7fa; color: #718096; font-size: 13px; }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><?php echo SITE_NAME; ?></h1>
            <p>Sign in to your account</p>
        </div>
        
        <div class="auth-body">
            <?php if (isset($error)): ?>
            <div class="message error">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
            <div class="message success">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="username">Username or Email</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="form-control" required placeholder="Enter username or email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Enter password">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
        </div>
        
        <div class="auth-footer">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
        </div>
    </div>
</div>

</body>
</html>
