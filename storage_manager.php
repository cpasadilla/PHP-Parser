<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Link Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .code { background: #f4f4f4; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #007cba; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #005a8b; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .status-card { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Storage Link Manager</h1>
        
        <?php
        $action = $_GET['action'] ?? 'check';
        $publicStoragePath = $_SERVER['DOCUMENT_ROOT'] . '/storage';
        $storageAppPublicPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';
        
        if ($action === 'create') {
            echo "<div class='status-card'>";
            echo "<h3>🔧 Creating Storage Link...</h3>";
            
            if (file_exists($publicStoragePath)) {
                echo "<div class='warning'>⚠️ Storage link already exists!</div>";
            } else {
                try {
                    // Try symbolic link first
                    if (symlink($storageAppPublicPath, $publicStoragePath)) {
                        echo "<div class='success'>✅ Symbolic link created successfully!</div>";
                    } else {
                        throw new Exception("Symlink failed");
                    }
                } catch (Exception $e) {
                    echo "<div class='warning'>⚠️ Symlink failed, copying files instead...</div>";
                    
                    try {
                        function copyDirectory($src, $dst) {
                            if (!file_exists($dst)) {
                                mkdir($dst, 0755, true);
                            }
                            
                            $dir = opendir($src);
                            while (($file = readdir($dir)) !== false) {
                                if ($file != '.' && $file != '..') {
                                    if (is_dir($src . '/' . $file)) {
                                        copyDirectory($src . '/' . $file, $dst . '/' . $file);
                                    } else {
                                        copy($src . '/' . $file, $dst . '/' . $file);
                                    }
                                }
                            }
                            closedir($dir);
                        }
                        
                        copyDirectory($storageAppPublicPath, $publicStoragePath);
                        echo "<div class='success'>✅ Files copied successfully!</div>";
                    } catch (Exception $e) {
                        echo "<div class='error'>❌ Failed to create storage link: " . $e->getMessage() . "</div>";
                    }
                }
            }
            echo "</div>";
        }
        ?>
        
        <div class="status-card">
            <h3>📊 Current Status</h3>
            
            <?php if (file_exists($publicStoragePath)): ?>
                <div class="success">✅ Storage link exists</div>
                <div class="code">
                    <strong>Location:</strong> <?php echo $publicStoragePath; ?><br>
                    <strong>Type:</strong> <?php echo is_link($publicStoragePath) ? 'Symbolic Link' : 'Directory'; ?><br>
                    <?php if (is_link($publicStoragePath)): ?>
                        <strong>Target:</strong> <?php echo readlink($publicStoragePath); ?><br>
                    <?php endif; ?>
                    <strong>Accessible via:</strong> <a href="/storage/" target="_blank"><?php echo $_SERVER['HTTP_HOST']; ?>/storage/</a>
                </div>
            <?php else: ?>
                <div class="error">❌ Storage link does not exist</div>
            <?php endif; ?>
            
            <?php if (file_exists($storageAppPublicPath)): ?>
                <div class="success">✅ Storage app/public directory exists</div>
                <div class="code">
                    <strong>Files in storage/app/public:</strong><br>
                    <?php
                    $files = scandir($storageAppPublicPath);
                    $fileCount = 0;
                    foreach ($files as $file) {
                        if ($file != '.' && $file != '..') {
                            echo "• " . $file . "<br>";
                            $fileCount++;
                        }
                    }
                    if ($fileCount === 0) {
                        echo "(No files found)";
                    }
                    ?>
                </div>
            <?php else: ?>
                <div class="error">❌ Storage app/public directory does not exist</div>
            <?php endif; ?>
        </div>
        
        <div class="status-card">
            <h3>🛠️ Actions</h3>
            
            <?php if (!file_exists($publicStoragePath)): ?>
                <a href="?action=create" class="btn">🔗 Create Storage Link</a>
            <?php else: ?>
                <div class="success">✅ Storage link is already set up!</div>
            <?php endif; ?>
            
            <a href="?action=check" class="btn">🔄 Refresh Status</a>
            
            <hr>
            
            <h4>🧪 Test File Upload</h4>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="test_file" accept="image/*">
                <button type="submit" name="upload_test" class="btn">📤 Test Upload</button>
            </form>
            
            <?php
            if (isset($_POST['upload_test']) && isset($_FILES['test_file'])) {
                $uploadDir = $storageAppPublicPath . '/';
                $uploadFile = $uploadDir . basename($_FILES['test_file']['name']);
                
                if (move_uploaded_file($_FILES['test_file']['tmp_name'], $uploadFile)) {
                    echo "<div class='success'>✅ Test file uploaded successfully!</div>";
                    echo "<div class='code'>File: " . basename($_FILES['test_file']['name']) . "</div>";
                    if (file_exists($publicStoragePath)) {
                        echo "<div class='info'>🌐 Access via: <a href='/storage/" . basename($_FILES['test_file']['name']) . "' target='_blank'>View File</a></div>";
                    }
                } else {
                    echo "<div class='error'>❌ Failed to upload test file</div>";
                }
            }
            ?>
        </div>
        
        <div class="status-card">
            <h3>📝 Laravel Code Examples</h3>
            <div class="code">
                <strong>Store a file:</strong><br>
                <code>$path = $request->file('avatar')->store('avatars', 'public');</code><br><br>
                
                <strong>Get storage URL:</strong><br>
                <code>$url = asset('storage/' . $path);</code><br><br>
                
                <strong>In Blade template:</strong><br>
                <code>&lt;img src="{{ asset('storage/avatars/filename.jpg') }}" alt="Avatar"&gt;</code>
            </div>
        </div>
        
        <div class="status-card">
            <p><strong>⚠️ Security Note:</strong> Delete this file after setting up your storage link!</p>
            <a href="?action=delete_self" class="btn btn-danger">🗑️ Delete This File</a>
        </div>
        
        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'delete_self') {
            if (unlink(__FILE__)) {
                echo "<div class='success'>✅ File deleted successfully!</div>";
                echo "<script>setTimeout(function(){ window.location.href = '/'; }, 2000);</script>";
            } else {
                echo "<div class='error'>❌ Could not delete file. Please remove manually.</div>";
            }
        }
        ?>
    </div>
</body>
</html>
