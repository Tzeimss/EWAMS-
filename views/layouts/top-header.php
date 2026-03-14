<header class="top-header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <span></span>
        </button>
        <h1 class="page-title"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
    </div>
    
    <div class="header-right">
        <div class="user-menu">
            <div class="user-info">
                <span class="user-name"><?php echo $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; ?></span>
                <span class="user-role"><?php echo ucfirst($_SESSION['role']); ?></span>
            </div>
        </div>
    </div>
</header>
