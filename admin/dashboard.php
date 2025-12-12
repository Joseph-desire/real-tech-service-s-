<?php
require_once __DIR__ . "/../config/auth.php";
require_admin();

$section = isset($_GET['section']) ? $_GET['section'] : "services";
$allowed = ["services","products","portfolio","team","orders"];
if (!in_array($section,$allowed)) $section = "services";

function upload_image($field, $folder) {
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return "";
  $name = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES[$field]['name']);
  $destDir = __DIR__ . "/../uploads/" . $folder;
  if (!is_dir($destDir)) mkdir($destDir, 0777, true);
  $dest = $destDir . "/" . $name;
  move_uploaded_file($_FILES[$field]['tmp_name'], $dest);
  return "uploads/$folder/$name";
}

// Handle delete
if (isset($_GET['del']) && isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  if ($section === "services") db_query("DELETE FROM services WHERE id=$id");
  if ($section === "products") db_query("DELETE FROM products WHERE id=$id");
  if ($section === "portfolio") db_query("DELETE FROM portfolio_items WHERE id=$id");
  if ($section === "team") db_query("DELETE FROM team_members WHERE id=$id");
  if ($section === "orders") db_query("DELETE FROM orders WHERE id=$id");
  header("Location: dashboard.php?section=$section");
  exit();
}

// Handle adds (simple for each section)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if ($section === "services") {
    $title = db_escape($_POST['title']);
    $desc  = db_escape($_POST['description']);
    $img   = upload_image("image", "services");
    db_query("INSERT INTO services (title, description, image_path) VALUES ('$title','$desc','$img')");
  }

  if ($section === "products") {
    $name = db_escape($_POST['name']);
    $desc = db_escape($_POST['description']);
    $price = (float)$_POST['price'];
    $img = upload_image("image", "products");
    db_query("INSERT INTO products (name, description, price, image_path) VALUES ('$name','$desc',$price,'$img')");
  }

  if ($section === "portfolio") {
    $title = db_escape($_POST['title']);
    $desc  = db_escape($_POST['description']);
    $img   = upload_image("image", "portfolio");
    db_query("INSERT INTO portfolio_items (title, description, image_path) VALUES ('$title','$desc','$img')");
  }

  if ($section === "team") {
    $name = db_escape($_POST['name']);
    $role = db_escape($_POST['role']);
    $bio  = db_escape($_POST['bio']);
    $img  = upload_image("image", "team");
    db_query("INSERT INTO team_members (name, role, bio, image_path) VALUES ('$name','$role','$bio','$img')");
  }

  if ($section === "orders") {
    // update status (optional)
    if (isset($_POST['order_id']) && isset($_POST['status'])) {
      $oid = (int)$_POST['order_id'];
      $st  = db_escape($_POST['status']);
      db_query("UPDATE orders SET status='$st' WHERE id=$oid");
    }
  }

  header("Location: dashboard.php?section=$section");
  exit();
}

// Fetch data for display
$rows = [];
if ($section === "services") $rows = db_query("SELECT * FROM services ORDER BY created_at DESC");
if ($section === "products") $rows = db_query("SELECT * FROM products ORDER BY created_at DESC");
if ($section === "portfolio") $rows = db_query("SELECT * FROM portfolio_items ORDER BY created_at DESC");
if ($section === "team") $rows = db_query("SELECT * FROM team_members ORDER BY created_at DESC");
if ($section === "orders") $rows = db_query("SELECT o.*, p.name AS product_name FROM orders o JOIN products p ON p.id=o.product_id ORDER BY o.created_at DESC");
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <title>Admin Dashboard - Real-Tech Services Ltd</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    /* Custom Admin Styles */
    * {
      scroll-behavior: smooth;
    }
    
    .sidebar {
      transition: all 0.3s ease;
    }
    
    @media (max-width: 768px) {
      .sidebar {
        position: fixed;
        left: -100%;
        top: 0;
        z-index: 50;
        height: 100vh;
        overflow-y: auto;
      }
      .sidebar.active {
        left: 0;
      }
      .mobile-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 40;
        display: none;
      }
      .mobile-overlay.active {
        display: block;
      }
    }
    
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
    
    .status-badge {
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-processing { background-color: #dbeafe; color: #1e40af; }
    .status-completed { background-color: #d1fae5; color: #065f46; }
    
    .form-input:focus, .form-select:focus, .form-textarea:focus {
      outline: none;
      ring: 2px;
      ring-color: #4f46e5;
      border-color: #4f46e5;
    }
    
    .stat-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    /* Image preview */
    .image-preview {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 0.5rem;
      border: 2px dashed #d1d5db;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #9ca3af;
      font-size: 0.875rem;
    }
    
    .image-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 0.375rem;
    }
    
    /* Mobile-friendly table */
    @media (max-width: 640px) {
      .mobile-table-row {
        display: flex;
        flex-direction: column;
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        background: white;
      }
      
      .mobile-table-row:last-child {
        border-bottom: none;
      }
    }
  </style>
</head>
<body class="bg-gray-50">
  <!-- Mobile Overlay -->
  <div id="mobileOverlay" class="mobile-overlay"></div>
  
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar w-64 bg-slate-900 text-white p-4 md:block">
      <!-- Logo -->
      <div class="flex items-center gap-3 pb-6 mb-6 border-b border-slate-700">
        <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center">
          <i class="fas fa-user-shield"></i>
        </div>
        <div>
          <h1 class="text-xl font-bold">Real-Tech Admin</h1>
          <p class="text-xs text-slate-400">Dashboard v2.0</p>
        </div>
      </div>
      
      <!-- User Info -->
      <div class="mb-8 p-3 rounded-lg bg-slate-800/50">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center">
            <i class="fas fa-user"></i>
          </div>
          <div>
            <p class="font-medium"><?php echo htmlspecialchars($_SESSION['admin_user']); ?></p>
            <p class="text-xs text-slate-400">Administrator</p>
          </div>
        </div>
      </div>
      
      <!-- Navigation -->
      <nav class="space-y-1 mb-8">
        <div class="text-xs uppercase tracking-wider text-slate-400 px-3 mb-2">Manage Content</div>
        
        <a href="dashboard.php?section=services" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $section=='services' ? 'bg-indigo-600' : 'hover:bg-slate-800'; ?>">
          <i class="fas fa-cogs w-5"></i>
          <span>Services</span>
        </a>
        
        <a href="dashboard.php?section=products" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $section=='products' ? 'bg-indigo-600' : 'hover:bg-slate-800'; ?>">
          <i class="fas fa-box w-5"></i>
          <span>Products</span>
        </a>
        
        <a href="dashboard.php?section=portfolio" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $section=='portfolio' ? 'bg-indigo-600' : 'hover:bg-slate-800'; ?>">
          <i class="fas fa-briefcase w-5"></i>
          <span>Portfolio</span>
        </a>
        
        <a href="dashboard.php?section=team" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $section=='team' ? 'bg-indigo-600' : 'hover:bg-slate-800'; ?>">
          <i class="fas fa-users w-5"></i>
          <span>Team</span>
        </a>
        
        <a href="dashboard.php?section=orders" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?php echo $section=='orders' ? 'bg-indigo-600' : 'hover:bg-slate-800'; ?>">
          <i class="fas fa-shopping-cart w-5"></i>
          <span>Orders</span>
          <?php 
            $pending_count = 0;
            if ($section === "orders" || true) {
              $pending_result = db_query("SELECT COUNT(*) as count FROM orders WHERE status='Pending'");
              if ($pending_row = mysql_fetch_assoc($pending_result)) {
                $pending_count = $pending_row['count'];
              }
            }
          ?>
          <?php if($pending_count > 0): ?>
            <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
              <?php echo $pending_count; ?>
            </span>
          <?php endif; ?>
        </a>
      </nav>
      
      <!-- Quick Stats -->
      <div class="p-3 rounded-lg bg-slate-800/50 mb-8">
        <div class="text-xs uppercase tracking-wider text-slate-400 mb-3">Quick Stats</div>
        <div class="space-y-3">
          <?php
            $services_count = mysql_num_rows(db_query("SELECT id FROM services"));
            $products_count = mysql_num_rows(db_query("SELECT id FROM products"));
            $orders_count = mysql_num_rows(db_query("SELECT id FROM orders"));
          ?>
          <div class="flex justify-between items-center">
            <span class="text-sm">Services</span>
            <span class="font-bold"><?php echo $services_count; ?></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Products</span>
            <span class="font-bold"><?php echo $products_count; ?></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm">Orders</span>
            <span class="font-bold"><?php echo $orders_count; ?></span>
          </div>
        </div>
      </div>
      
      <!-- Bottom Links -->
      <div class="mt-auto pt-6 border-t border-slate-700">
        <a href="/realtech/public/index.php" target="_blank" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-colors mb-2">
          <i class="fas fa-external-link-alt w-5"></i>
          <span>View Site</span>
        </a>
        
        <a href="/realtech/admin/logout.php" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-red-900/30 transition-colors text-red-300">
          <i class="fas fa-sign-out-alt w-5"></i>
          <span>Logout</span>
        </a>
      </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1">
      <!-- Top Bar -->
      <div class="bg-white border-b px-4 py-3">
        <div class="flex items-center justify-between">
          <!-- Mobile Menu Button -->
          <button id="mobileMenuButton" class="md:hidden text-slate-700 text-xl">
            <i class="fas fa-bars"></i>
          </button>
          
          <!-- Breadcrumb -->
          <div class="flex items-center gap-2 text-sm">
            <a href="dashboard.php" class="text-indigo-600">Dashboard</a>
            <span class="text-slate-400">/</span>
            <span class="text-slate-700 font-medium capitalize"><?php echo $section; ?></span>
          </div>
          
          <!-- Actions -->
          <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 hidden md:inline">Last login: <?php echo date('Y-m-d H:i'); ?></span>
            <a href="/realtech/public/index.php" target="_blank" 
               class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-sm hover:bg-slate-200 transition-colors flex items-center gap-2">
              <i class="fas fa-external-link-alt text-xs"></i>
              <span class="hidden sm:inline">View Site</span>
            </a>
          </div>
        </div>
      </div>
      
      <!-- Main Content Area -->
      <div class="p-4 md:p-6">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
              <h2 class="text-2xl font-bold">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_user']); ?>!</h2>
              <p class="text-indigo-100 mt-1">Manage your website content from this dashboard</p>
            </div>
            <div class="flex items-center gap-3">
              <div class="text-right">
                <div class="text-sm opacity-90">Active Section</div>
                <div class="text-xl font-bold capitalize"><?php echo $section; ?></div>
              </div>
              <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <?php 
                  $icons = [
                    'services' => 'fa-cogs',
                    'products' => 'fa-box',
                    'portfolio' => 'fa-briefcase',
                    'team' => 'fa-users',
                    'orders' => 'fa-shopping-cart'
                  ];
                ?>
                <i class="fas <?php echo $icons[$section]; ?> text-xl"></i>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Add Form Section -->
        <div class="bg-white rounded-xl shadow-sm border p-5 md:p-6 mb-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
              <?php 
                $titles = [
                  'services' => 'Add New Service',
                  'products' => 'Add New Product',
                  'portfolio' => 'Add Portfolio Item',
                  'team' => 'Add Team Member',
                  'orders' => 'Order Management'
                ];
              ?>
              <i class="fas <?php echo $icons[$section]; ?> text-indigo-600"></i>
              <?php echo $titles[$section]; ?>
            </h2>
            <div class="text-sm text-slate-500">
              <i class="fas fa-database mr-1"></i>
              <?php
                $counts = [
                  'services' => $services_count,
                  'products' => $products_count,
                  'portfolio' => mysql_num_rows(db_query("SELECT id FROM portfolio_items")),
                  'team' => mysql_num_rows(db_query("SELECT id FROM team_members")),
                  'orders' => $orders_count
                ];
              ?>
              Total: <?php echo $counts[$section]; ?> items
            </div>
          </div>
          
          <?php if ($section === "services"): ?>
            <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Service Title *</label>
                  <input type="text" name="title" placeholder="e.g., CCTV Installation" 
                         class="w-full border rounded-lg px-4 py-3 form-input focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Description *</label>
                  <textarea name="description" rows="4" placeholder="Describe the service in detail..."
                            class="w-full border rounded-lg px-4 py-3 form-textarea focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Service Image *</label>
                  <div class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center">
                    <div id="imagePreview" class="image-preview mx-auto mb-3">
                      <span>No image selected</span>
                    </div>
                    <input type="file" name="image" id="imageInput" 
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                           accept="image/*" required
                           onchange="previewImage(event)">
                    <p class="text-xs text-slate-500 mt-2">Recommended: 800x600px, JPG/PNG, max 2MB</p>
                  </div>
                </div>
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white rounded-lg px-6 py-3.5 font-semibold hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                  <i class="fas fa-plus"></i> Add Service
                </button>
              </div>
            </form>

          <?php elseif ($section === "products"): ?>
            <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Product Name *</label>
                  <input type="text" name="name" placeholder="e.g., Network Router" 
                         class="w-full border rounded-lg px-4 py-3 form-input focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Description *</label>
                  <textarea name="description" rows="3" placeholder="Product features, specifications..."
                            class="w-full border rounded-lg px-4 py-3 form-textarea focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Price (RWF) *</label>
                  <div class="relative">
                    <span class="absolute left-3 top-3 text-slate-500">RWF</span>
                    <input type="number" name="price" step="0.01" min="0" placeholder="0.00" 
                           class="w-full border rounded-lg px-4 py-3 pl-16 form-input focus:border-indigo-500 focus:ring-indigo-500" required>
                  </div>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Product Image *</label>
                  <div class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center">
                    <div id="imagePreview" class="image-preview mx-auto mb-3">
                      <span>No image selected</span>
                    </div>
                    <input type="file" name="image" id="imageInput" 
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                           accept="image/*" required
                           onchange="previewImage(event)">
                    <p class="text-xs text-slate-500 mt-2">Recommended: 800x600px, JPG/PNG, max 2MB</p>
                  </div>
                </div>
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white rounded-lg px-6 py-3.5 font-semibold hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                  <i class="fas fa-plus"></i> Add Product
                </button>
              </div>
            </form>

          <?php elseif ($section === "portfolio"): ?>
            <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Project Title *</label>
                  <input type="text" name="title" placeholder="e.g., Office Network Setup" 
                         class="w-full border rounded-lg px-4 py-3 form-input focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Description *</label>
                  <textarea name="description" rows="5" placeholder="Project details, client, technologies used..."
                            class="w-full border rounded-lg px-4 py-3 form-textarea focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Project Image *</label>
                  <div class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center">
                    <div id="imagePreview" class="image-preview mx-auto mb-3">
                      <span>No image selected</span>
                    </div>
                    <input type="file" name="image" id="imageInput" 
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                           accept="image/*" required
                           onchange="previewImage(event)">
                    <p class="text-xs text-slate-500 mt-2">Recommended: 1200x800px, JPG/PNG, max 2MB</p>
                  </div>
                </div>
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white rounded-lg px-6 py-3.5 font-semibold hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                  <i class="fas fa-plus"></i> Add Portfolio Item
                </button>
              </div>
            </form>

          <?php elseif ($section === "team"): ?>
            <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                  <input type="text" name="name" placeholder="e.g., John Doe" 
                         class="w-full border rounded-lg px-4 py-3 form-input focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Role / Position *</label>
                  <input type="text" name="role" placeholder="e.g., Network Engineer" 
                         class="w-full border rounded-lg px-4 py-3 form-input focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Bio *</label>
                  <textarea name="bio" rows="4" placeholder="Professional background, skills, experience..."
                            class="w-full border rounded-lg px-4 py-3 form-textarea focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-1">Profile Photo *</label>
                  <div class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center">
                    <div id="imagePreview" class="image-preview mx-auto mb-3">
                      <span>No image selected</span>
                    </div>
                    <input type="file" name="image" id="imageInput" 
                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                           accept="image/*" required
                           onchange="previewImage(event)">
                    <p class="text-xs text-slate-500 mt-2">Recommended: 400x400px, JPG/PNG, max 2MB</p>
                  </div>
                </div>
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white rounded-lg px-6 py-3.5 font-semibold hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                  <i class="fas fa-plus"></i> Add Team Member
                </button>
              </div>
            </form>

          <?php else: // orders ?>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                <div>
                  <h3 class="font-medium text-blue-900">Order Management</h3>
                  <p class="text-sm text-blue-700 mt-1">
                    Use the status dropdown in each order below to update its status. Orders are automatically created when customers purchase products.
                  </p>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Data Table Section -->
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
          <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-800">
              <?php 
                $table_titles = [
                  'services' => 'All Services',
                  'products' => 'All Products',
                  'portfolio' => 'Portfolio Items',
                  'team' => 'Team Members',
                  'orders' => 'Customer Orders'
                ];
              ?>
              <i class="fas <?php echo $icons[$section]; ?> text-indigo-600 mr-2"></i>
              <?php echo $table_titles[$section]; ?>
              <span class="text-sm font-normal text-slate-500 ml-2">(<?php echo $counts[$section]; ?> records)</span>
            </h2>
            
            <?php if ($section === "orders"): ?>
              <div class="flex gap-2">
                <span class="inline-flex items-center gap-1 text-sm">
                  <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Pending
                </span>
                <span class="inline-flex items-center gap-1 text-sm">
                  <span class="w-2 h-2 rounded-full bg-blue-500"></span> Processing
                </span>
                <span class="inline-flex items-center gap-1 text-sm">
                  <span class="w-2 h-2 rounded-full bg-green-500"></span> Completed
                </span>
              </div>
            <?php endif; ?>
          </div>
          
          <!-- Desktop Table -->
          <div class="hidden md:block table-responsive">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Details</th>
                  <?php if ($section === "orders"): ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Customer Info</th>
                    <th class="px6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                  <?php else: ?>
                    <th class="px6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Created</th>
                  <?php endif; ?>
                  <th class="px6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-slate-200">
                <?php while($r = mysql_fetch_assoc($rows)): ?>
                  <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">
                      #<?php echo (int)$r['id']; ?>
                    </td>
                    
                    <td class="px-6 py-4">
                      <div class="flex items-start gap-4">
                        <?php if ($section !== "orders" && !empty($r['image_path'])): ?>
                          <img src="/realtech/<?php echo htmlspecialchars($r['image_path']); ?>" 
                               class="w-16 h-16 rounded-lg object-cover border">
                        <?php endif; ?>
                        <div>
                          <div class="font-semibold text-slate-900">
                            <?php
                              if ($section === "services") echo htmlspecialchars($r['title']);
                              if ($section === "products") echo htmlspecialchars($r['name']);
                              if ($section === "portfolio") echo htmlspecialchars($r['title']);
                              if ($section === "team") echo htmlspecialchars($r['name']);
                              if ($section === "orders") echo htmlspecialchars($r['product_name']);
                            ?>
                          </div>
                          <div class="text-sm text-slate-600 mt-1 line-clamp-2">
                            <?php
                              if ($section === "products") echo "Price: RWF " . number_format($r['price'],0) . " · ";
                              if ($section === "team") echo "Role: " . htmlspecialchars($r['role']) . " · ";
                              if ($section === "orders") {
                                echo "Qty: " . (int)$r['qty'] . " · Total: RWF " . number_format($r['total'],0);
                              } else {
                                echo htmlspecialchars(substr($section === "team" ? $r['bio'] : $r['description'], 0, 120)) . "...";
                              }
                            ?>
                          </div>
                        </div>
                      </div>
                    </td>
                    
                    <?php if ($section === "orders"): ?>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm">
                          <div class="font-medium"><?php echo htmlspecialchars($r['customer_name']); ?></div>
                          <div class="text-slate-500"><?php echo htmlspecialchars($r['phone']); ?></div>
                          <div class="text-xs text-slate-400 mt-1">
                            <?php echo date('M d, Y', strtotime($r['created_at'])); ?>
                          </div>
                        </div>
                      </td>
                      
                      <td class="px-6 py-4 whitespace-nowrap">
                        <form method="POST" class="flex gap-2 items-center">
                          <input type="hidden" name="order_id" value="<?php echo (int)$r['id']; ?>">
                          <select name="status" 
                                  class="border rounded-lg px-3 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 form-select">
                            <option value="Pending" <?php if($r['status']=='Pending') echo 'selected'; ?>>Pending</option>
                            <option value="Processing" <?php if($r['status']=='Processing') echo 'selected'; ?>>Processing</option>
                            <option value="Completed" <?php if($r['status']=='Completed') echo 'selected'; ?>>Completed</option>
                          </select>
                          <button type="submit" 
                                  class="bg-indigo-600 text-white rounded-lg px-3 py-1.5 text-sm hover:bg-indigo-700 transition-colors">
                            Update
                          </button>
                        </form>
                      </td>
                    <?php else: ?>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        <?php echo date('M d, Y', strtotime($r['created_at'])); ?>
                      </td>
                    <?php endif; ?>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                      <div class="flex gap-2">
                        <?php if ($section !== "orders"): ?>
                          <a href="#" 
                             class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors text-sm">
                            <i class="fas fa-edit mr-1"></i> Edit
                          </a>
                        <?php endif; ?>
                        <a href="dashboard.php?section=<?php echo $section; ?>&del=1&id=<?php echo (int)$r['id']; ?>" 
                           onclick="return confirm('Are you sure you want to delete this item?');"
                           class="px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition-colors text-sm">
                          <i class="fas fa-trash mr-1"></i> Delete
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Mobile Cards View -->
          <div class="md:hidden divide-y divide-slate-200">
            <?php 
              // Reset pointer for mobile view
              mysql_data_seek($rows, 0);
              while($r = mysql_fetch_assoc($rows)): 
            ?>
              <div class="mobile-table-row">
                <div class="flex justify-between items-start mb-3">
                  <div>
                    <span class="text-xs font-medium text-slate-500">ID #<?php echo (int)$r['id']; ?></span>
                    <div class="font-semibold text-slate-900 mt-1">
                      <?php
                        if ($section === "services") echo htmlspecialchars($r['title']);
                        if ($section === "products") echo htmlspecialchars($r['name']);
                        if ($section === "portfolio") echo htmlspecialchars($r['title']);
                        if ($section === "team") echo htmlspecialchars($r['name']);
                        if ($section === "orders") echo htmlspecialchars($r['product_name']);
                      ?>
                    </div>
                  </div>
                  <span class="text-xs text-slate-500">
                    <?php echo date('M d, Y', strtotime($r['created_at'])); ?>
                  </span>
                </div>
                
                <div class="text-sm text-slate-600 mb-4">
                  <?php
                    if ($section === "products") echo "<strong>Price:</strong> RWF " . number_format($r['price'],0) . "<br>";
                    if ($section === "team") echo "<strong>Role:</strong> " . htmlspecialchars($r['role']) . "<br>";
                    if ($section === "orders") {
                      echo "<strong>Customer:</strong> " . htmlspecialchars($r['customer_name']) . "<br>";
                      echo "<strong>Phone:</strong> " . htmlspecialchars($r['phone']) . "<br>";
                      echo "<strong>Qty:</strong> " . (int)$r['qty'] . " · <strong>Total:</strong> RWF " . number_format($r['total'],0);
                    } else {
                      echo htmlspecialchars(substr($section === "team" ? $r['bio'] : $r['description'], 0, 200)) . "...";
                    }
                  ?>
                </div>
                
                <?php if ($section === "orders"): ?>
                  <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                      <span class="font-medium">Status:</span>
                      <span class="status-badge status-<?php echo strtolower($r['status']); ?>">
                        <?php echo $r['status']; ?>
                      </span>
                    </div>
                    <form method="POST" class="space-y-2">
                      <input type="hidden" name="order_id" value="<?php echo (int)$r['id']; ?>">
                      <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="Pending" <?php if($r['status']=='Pending') echo 'selected'; ?>>Pending</option>
                        <option value="Processing" <?php if($r['status']=='Processing') echo 'selected'; ?>>Processing</option>
                        <option value="Completed" <?php if($r['status']=='Completed') echo 'selected'; ?>>Completed</option>
                      </select>
                      <button type="submit" 
                              class="w-full bg-indigo-600 text-white rounded-lg px-4 py-2 text-sm font-medium">
                        Update Status
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
                
                <div class="flex gap-2">
                  <?php if ($section !== "orders"): ?>
                    <a href="#" class="flex-1 px-4 py-2 rounded-lg bg-blue-50 text-blue-700 text-center text-sm font-medium">
                      <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                  <?php endif; ?>
                  <a href="dashboard.php?section=<?php echo $section; ?>&del=1&id=<?php echo (int)$r['id']; ?>" 
                     onclick="return confirm('Are you sure you want to delete this item?');"
                     class="flex-1 px-4 py-2 rounded-lg bg-red-50 text-red-700 text-center text-sm font-medium">
                    <i class="fas fa-trash mr-2"></i> Delete
                  </a>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
          
          <?php if (mysql_num_rows($rows) == 0): ?>
            <div class="py-12 text-center">
              <div class="text-slate-400 text-4xl mb-4">
                <i class="fas fa-inbox"></i>
              </div>
              <h3 class="text-lg font-medium text-slate-700 mb-2">No <?php echo $section; ?> found</h3>
              <p class="text-slate-500 max-w-sm mx-auto">
                <?php if ($section === "orders"): ?>
                  No orders have been placed yet. Orders will appear here when customers make purchases.
                <?php else: ?>
                  Add your first item using the form above to get started.
                <?php endif; ?>
              </p>
            </div>
          <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="mt-6 text-center text-sm text-slate-500">
          <p>Real-Tech Services Ltd Admin Panel © <?php echo date("Y"); ?> | v2.0</p>
          <p class="mt-1 text-xs">Server Time: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');
    
    if (mobileMenuButton) {
      mobileMenuButton.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
      });
      
      mobileOverlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        mobileOverlay.classList.remove('active');
      });
    }
    
    // Image preview function
    function previewImage(event) {
      const input = event.target;
      const preview = document.getElementById('imagePreview');
      
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        }
        
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.innerHTML = '<span>No image selected</span>';
      }
    }
    
    // Status badge colors
    document.querySelectorAll('.status-badge').forEach(badge => {
      const status = badge.textContent.trim().toLowerCase();
      badge.classList.add(`status-${status}`);
    });
    
    // Form validation
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(e) {
        const requiredInputs = this.querySelectorAll('[required]');
        let valid = true;
        
        requiredInputs.forEach(input => {
          if (!input.value.trim()) {
            valid = false;
            input.classList.add('border-red-500');
          } else {
            input.classList.remove('border-red-500');
          }
        });
        
        if (!valid) {
          e.preventDefault();
          alert('Please fill in all required fields.');
        }
      });
    });
    
    // File size validation
    document.querySelectorAll('input[type="file"]').forEach(input => {
      input.addEventListener('change', function() {
        const file = this.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (file && file.size > maxSize) {
          alert('File size must be less than 2MB');
          this.value = '';
          
          const preview = document.getElementById('imagePreview');
          if (preview) {
            preview.innerHTML = '<span>No image selected</span>';
          }
        }
      });
    });
    
    // Auto-hide success messages
    setTimeout(() => {
      const alerts = document.querySelectorAll('.bg-green-50');
      alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      });
    }, 3000);
    
    // Confirm before delete
    document.querySelectorAll('a[onclick*="confirm"]').forEach(link => {
      link.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
          e.preventDefault();
        }
      });
    });
    
    // Update page title with section
    document.title = `Admin - ${document.querySelector('h2.text-xl').textContent} - Real-Tech`;
  </script>
</body>
</html>