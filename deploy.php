<?php
// deploy.php
// Basic deployment script for an application on EC2

// Set variables
$repo = "https://github.com/yourusername/your-app.git"; // Replace with your repo
$deployPath = "/var/www/html/myapp"; // Target deployment directory
$branch = "main"; // Branch to deploy

function runCommand($cmd) {
    echo "Running: $cmd\n";
    $output = [];
    $return_var = 0;
    exec($cmd, $output, $return_var);
    if ($return_var !== 0) {
        echo "Error running command: $cmd\n";
        echo implode("\n", $output);
        exit($return_var);
    }
    return $output;
}

// Ensure git is installed
runCommand("which git || sudo apt update && sudo apt install -y git");

// Create deployment directory if it doesn't exist
if (!is_dir($deployPath)) {
    runCommand("sudo mkdir -p $deployPath");
    runCommand("sudo chown -R $USER:$USER $deployPath");
}

// If directory is already a git repo, pull latest code
if (is_dir("$deployPath/.git")) {
    runCommand("cd $deployPath && git reset --hard && git pull origin $branch");
} else {
    // Clone repo
    runCommand("git clone -b $branch $repo $deployPath");
}

// Set permissions
runCommand("sudo chown -R www-data:www-data $deployPath");
runCommand("sudo chmod -R 755 $deployPath");

// Optional: restart Apache/Nginx
runCommand("sudo systemctl restart apache2"); // Change to nginx if using Nginx

echo "Deployment completed successfully!\n";
?>
