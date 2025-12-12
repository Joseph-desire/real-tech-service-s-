<?php
require_once __DIR__ . "/../config/db.php";

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product = mysql_fetch_assoc(db_query("SELECT * FROM products WHERE id=" . $product_id));

if (!$product) { 
  header("Location: /realtech/public/index.php");
  exit();
}

// Check if columns exist
$order_columns = db_query("SHOW COLUMNS FROM orders");
$columns = [];
while($col = mysql_fetch_assoc($order_columns)) {
    $columns[] = $col['Field'];
}

$has_email = in_array('email', $columns);
$has_address = in_array('address', $columns);

$msg = "";
$order_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = db_escape($_POST['customer_name']);
  $phone = db_escape($_POST['phone']);
  $qty = max(1, (int)$_POST['qty']);
  $email = $has_email ? db_escape($_POST['email']) : '';
  $address = $has_address ? db_escape($_POST['address']) : '';

  $total = $qty * (float)$product['price'];

  if ($has_email && $has_address) {
    // Both columns exist
    db_query("INSERT INTO orders (customer_name, phone, email, address, product_id, qty, total) VALUES
              ('$name', '$phone', '$email', '$address', $product_id, $qty, $total)");
  } elseif ($has_email) {
    // Only email column exists
    db_query("INSERT INTO orders (customer_name, phone, email, product_id, qty, total) VALUES
              ('$name', '$phone', '$email', $product_id, $qty, $total)");
  } elseif ($has_address) {
    // Only address column exists
    db_query("INSERT INTO orders (customer_name, phone, address, product_id, qty, total) VALUES
              ('$name', '$phone', '$address', $product_id, $qty, $total)");
  } else {
    // Original columns only
    db_query("INSERT INTO orders (customer_name, phone, product_id, qty, total) VALUES
              ('$name', '$phone', $product_id, $qty, $total)");
  }
  
  $order_id = mysql_insert_id();
  $msg = "Order placed successfully! We will contact you soon.";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
  <title>Order <?php echo htmlspecialchars($product['name']); ?> - Real-Tech Services Ltd</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    /* Custom styles */
    * {
      scroll-behavior: smooth;
    }
    
    .order-step {
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      background: #e5e7eb;
      color: #6b7280;
    }
    
    .order-step.active {
      background: #4f46e5;
      color: white;
    }
    
    .order-step.completed {
      background: #10b981;
      color: white;
    }
    
    .step-line {
      flex: 1;
      height: 2px;
      background: #e5e7eb;
      margin: 0 0.5rem;
    }
    
    .step-line.active {
      background: #4f46e5;
    }
    
    .step-line.completed {
      background: #10b981;
    }
    
    .product-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-card:hover {
      transform: translateY(-2px);
    }
    
    input:focus, select:focus, textarea:focus {
      outline: none;
      ring: 2px;
      ring-color: #4f46e5;
      border-color: #4f46e5;
    }
    
    .quantity-btn {
      width: 2.5rem;
      height: 2.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f3f4f6;
      border-radius: 0.375rem;
      font-size: 1.25rem;
      font-weight: 500;
      color: #374151;
      cursor: pointer;
      user-select: none;
    }
    
    .quantity-btn:hover {
      background: #e5e7eb;
    }
    
    .quantity-input {
      width: 3.5rem;
      text-align: center;
      font-size: 1.125rem;
      font-weight: 600;
    }
    
    @media (max-width: 640px) {
      .step-container {
        padding: 0 0.5rem;
      }
      
      .step-label {
        font-size: 0.75rem;
      }
    }
    
    /* Success animation */
    .success-checkmark {
      width: 80px;
      height: 80px;
      margin: 0 auto;
      position: relative;
    }
    
    .check-icon {
      width: 80px;
      height: 80px;
      position: relative;
      border-radius: 50%;
      box-sizing: content-box;
      border: 4px solid #10b981;
    }
    
    .check-icon::after {
      content: '';
      position: absolute;
      width: 35px;
      height: 70px;
      border-top: 4px solid #10b981;
      border-right: 4px solid #10b981;
      transform: scaleX(-1) rotate(135deg);
      transform-origin: left top;
      left: 22px;
      top: 38px;
    }
  </style>
</head>
<body class="bg-gray-50">
  <!-- Header -->
  <header class="bg-white shadow-sm sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 py-3">
      <div class="flex items-center justify-between">
        <a href="/realtech/public/index.php" class="flex items-center gap-3 text-indigo-700 hover:text-indigo-800">
          <i class="fas fa-arrow-left"></i>
          <span class="font-medium">Back to Home</span>
        </a>
        
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
            <i class="fas fa-shopping-cart text-white"></i>
          </div>
          <span class="font-bold text-slate-800">Order Now</span>
        </div>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto px-4 py-6 md:py-10">
    <!-- Order Steps -->
    <div class="mb-8 md:mb-12">
      <div class="flex items-center justify-center step-container">
        <div class="flex items-center w-full max-w-md">
          <!-- Step 1 -->
          <div class="flex flex-col items-center">
            <div class="order-step <?php echo empty($msg) ? 'active' : 'completed'; ?>">
              <span>1</span>
            </div>
            <span class="step-label mt-2 text-sm font-medium text-slate-700">Product</span>
          </div>
          
          <div class="step-line <?php echo empty($msg) ? '' : 'completed'; ?>"></div>
          
          <!-- Step 2 -->
          <div class="flex flex-col items-center">
            <div class="order-step <?php echo empty($msg) ? '' : ($msg ? 'completed' : 'active'); ?>">
              <span>2</span>
            </div>
            <span class="step-label mt-2 text-sm font-medium text-slate-700">Details</span>
          </div>
          
          <div class="step-line <?php echo $msg ? 'completed' : ''; ?>"></div>
          
          <!-- Step 3 -->
          <div class="flex flex-col items-center">
            <div class="order-step <?php echo $msg ? 'active' : ''; ?>">
              <span>3</span>
            </div>
            <span class="step-label mt-2 text-sm font-medium text-slate-700">Confirmation</span>
          </div>
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
      <!-- Left Column: Product Details -->
      <div class="lg:col-span-2">
        <?php if ($msg): ?>
          <!-- Success Message -->
          <div class="bg-white rounded-2xl shadow-lg border p-6 md:p-8 text-center">
            <div class="success-checkmark mb-6">
              <div class="check-icon"></div>
            </div>
            
            <h2 class="text-3xl font-bold text-slate-800 mb-3">Order Confirmed!</h2>
            <p class="text-lg text-slate-600 mb-6">Thank you for your order. We'll contact you soon.</p>
            
            <div class="bg-green-50 border border-green-200 rounded-xl p-5 max-w-md mx-auto mb-8">
              <div class="flex items-center justify-between mb-3">
                <span class="text-slate-700">Order ID:</span>
                <span class="font-bold text-lg">#<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?></span>
              </div>
              <div class="flex items-center justify-between mb-3">
                <span class="text-slate-700">Product:</span>
                <span class="font-semibold"><?php echo htmlspecialchars($product['name']); ?></span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-slate-700">Estimated Contact:</span>
                <span class="font-semibold">Within 24 hours</span>
              </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
              <a href="/realtech/public/index.php#products" 
                 class="px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-box"></i> Browse More Products
              </a>
              <a href="/realtech/public/index.php#contact" 
                 class="px-6 py-3 rounded-lg bg-white border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-headset"></i> Contact Support
              </a>
            </div>
            
            <div class="mt-8 pt-6 border-t border-slate-200">
              <p class="text-sm text-slate-500 mb-2">What happens next?</p>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="flex flex-col items-center p-3 bg-blue-50 rounded-lg">
                  <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                    <i class="fas fa-phone text-blue-600"></i>
                  </div>
                  <span class="font-medium">1. We'll Call You</span>
                  <span class="text-xs text-slate-600 mt-1">Within 24 hours</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-blue-50 rounded-lg">
                  <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                    <i class="fas fa-calendar-check text-blue-600"></i>
                  </div>
                  <span class="font-medium">2. Schedule Installation</span>
                  <span class="text-xs text-slate-600 mt-1">Pick convenient time</span>
                </div>
                <div class="flex flex-col items-center p-3 bg-blue-50 rounded-lg">
                  <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mb-2">
                    <i class="fas fa-tools text-blue-600"></i>
                  </div>
                  <span class="font-medium">3. Professional Service</span>
                  <span class="text-xs text-slate-600 mt-1">Expert installation</span>
                </div>
              </div>
            </div>
          </div>
        <?php else: ?>
          <!-- Order Form -->
          <div class="bg-white rounded-2xl shadow-lg border overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
              <h2 class="text-2xl font-bold">Complete Your Order</h2>
              <p class="text-indigo-100 mt-1">Fill in your details to place the order</p>
            </div>
            
            <form method="POST" class="p-6 md:p-8 space-y-6">
              <!-- Customer Information -->
              <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                  <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-user text-indigo-600"></i>
                  </div>
                  Customer Information
                </h3>
                
                <div class="grid md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                      Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="customer_name" 
                           class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="John Doe" required>
                  </div>
                  
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                      Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" 
                           class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="+250 788 123 456" required>
                  </div>
                  
                  <?php if ($has_email): ?>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                      Email Address
                    </label>
                    <input type="email" name="email" 
                           class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="john@example.com">
                  </div>
                  <?php endif; ?>
                  
                  <?php if ($has_address): ?>
                  <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                      Delivery Address
                    </label>
                    <input type="text" name="address" 
                           class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500" 
                           placeholder="Street, City, Rwanda">
                  </div>
                  <?php endif; ?>
                  
                  <!-- Hidden fields if columns don't exist -->
                  <?php if (!$has_email): ?>
                    <input type="hidden" name="email" value="">
                  <?php endif; ?>
                  
                  <?php if (!$has_address): ?>
                    <input type="hidden" name="address" value="">
                  <?php endif; ?>
                </div>
              </div>
              
              <!-- Order Details -->
              <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                  <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-indigo-600"></i>
                  </div>
                  Order Details
                </h3>
                
                <div class="bg-slate-50 rounded-xl p-4 md:p-6">
                  <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-slate-700">
                      Quantity <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="flex items-center gap-2">
                      <button type="button" class="quantity-btn decrement">
                        <i class="fas fa-minus"></i>
                      </button>
                      <input type="number" name="qty" value="1" min="1" 
                             class="quantity-input border border-slate-300 rounded-lg py-2">
                      <button type="button" class="quantity-btn increment">
                        <i class="fas fa-plus"></i>
                      </button>
                    </div>
                  </div>
                  
                  <div class="bg-white rounded-lg p-4 border border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-slate-600">Unit Price:</span>
                      <span class="font-bold">RWF <?php echo number_format($product['price'], 0); ?></span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-slate-600">Quantity:</span>
                      <span class="font-bold" id="quantity-display">1</span>
                    </div>
                    <div class="border-t pt-2 flex items-center justify-between">
                      <span class="text-lg font-bold text-slate-800">Total Amount:</span>
                      <span class="text-2xl font-bold text-indigo-700" id="total-amount">
                        RWF <?php echo number_format($product['price'], 0); ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Payment & Terms -->
              <div>
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                  <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-file-contract text-indigo-600"></i>
                  </div>
                  Terms & Conditions
                </h3>
                
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                  <div class="flex items-start gap-3">
                    <input type="checkbox" id="terms" class="mt-1" required>
                    <label for="terms" class="text-sm text-slate-700">
                      I agree to the <a href="#" class="text-indigo-600 hover:underline">terms and conditions</a>. 
                      I understand that a representative will contact me to confirm the order and schedule installation/delivery. 
                      Payment will be requested upon confirmation.
                    </label>
                  </div>
                </div>
              </div>
              
              <!-- Submit Button -->
              <button type="submit" 
                      class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl py-4 px-6 font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-300 flex items-center justify-center gap-3 shadow-lg hover:shadow-xl">
                <i class="fas fa-paper-plane"></i>
                <span>Place Order Now</span>
              </button>
              
              <p class="text-center text-sm text-slate-500 mt-4">
                <i class="fas fa-lock mr-1"></i> Your information is secure and will not be shared with third parties.
              </p>
            </form>
          </div>
        <?php endif; ?>
      </div>
      
      <!-- Right Column: Order Summary -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-lg border p-6 sticky top-24">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-clipboard-list text-indigo-600"></i>
            Order Summary
          </h3>
          
          <!-- Product Card -->
          <div class="product-card bg-slate-50 rounded-xl p-4 mb-6 border border-slate-200">
            <div class="flex gap-4">
              <?php if (!empty($product['image_path'])): ?>
                <img src="/realtech/<?php echo htmlspecialchars($product['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     class="w-20 h-20 rounded-lg object-cover border">
              <?php else: ?>
                <div class="w-20 h-20 rounded-lg bg-indigo-100 flex items-center justify-center">
                  <i class="fas fa-box text-indigo-600 text-2xl"></i>
                </div>
              <?php endif; ?>
              
              <div class="flex-1">
                <h4 class="font-bold text-slate-800"><?php echo htmlspecialchars($product['name']); ?></h4>
                <p class="text-sm text-slate-600 mt-1 line-clamp-2">
                  <?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...
                </p>
                <div class="mt-2 font-bold text-indigo-700 text-lg">
                  RWF <?php echo number_format($product['price'], 0); ?>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Price Breakdown -->
          <div class="space-y-3 mb-6">
            <div class="flex justify-between">
              <span class="text-slate-600">Unit Price</span>
              <span class="font-medium">RWF <?php echo number_format($product['price'], 0); ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-600">Quantity</span>
              <span class="font-medium" id="sidebar-quantity">1</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-600">Delivery</span>
              <span class="font-medium text-green-600">To be confirmed</span>
            </div>
            <div class="border-t pt-3">
              <div class="flex justify-between">
                <span class="text-lg font-bold text-slate-800">Total</span>
                <span class="text-2xl font-bold text-indigo-700" id="sidebar-total">
                  RWF <?php echo number_format($product['price'], 0); ?>
                </span>
              </div>
            </div>
          </div>
          
          <!-- Benefits -->
          <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <h4 class="font-bold text-green-800 mb-3 flex items-center gap-2">
              <i class="fas fa-award"></i>
              Why Order From Us?
            </h4>
            <ul class="space-y-2 text-sm">
              <li class="flex items-start gap-2">
                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                <span>Professional installation included</span>
              </li>
              <li class="flex items-start gap-2">
                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                <span>1-year warranty on all products</span>
              </li>
              <li class="flex items-start gap-2">
                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                <span>24/7 technical support</span>
              </li>
              <li class="flex items-start gap-2">
                <i class="fas fa-check-circle text-green-600 mt-1"></i>
                <span>Free consultation</span>
              </li>
            </ul>
          </div>
          
          <!-- Contact Info -->
          <div class="border-t pt-6">
            <h4 class="font-bold text-slate-800 mb-3">Need Help?</h4>
            <div class="space-y-3 text-sm">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                  <i class="fas fa-phone text-indigo-600"></i>
                </div>
                <div>
                  <div class="font-medium">Call Us</div>
                  <div class="text-slate-600">+250 788 123 456</div>
                </div>
              </div>
              
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                  <i class="fas fa-envelope text-indigo-600"></i>
                </div>
                <div>
                  <div class="font-medium">Email Us</div>
                  <div class="text-slate-600">sales@realtech.rw</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-slate-900 text-white mt-12 py-8">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <p class="text-slate-400 text-sm">
        <i class="fas fa-shield-alt mr-1"></i> Secure Order Processing • 
        <i class="fas fa-lock mr-1 ml-4"></i> Your Privacy Protected • 
        <i class="fas fa-truck mr-1 ml-4"></i> Professional Delivery
      </p>
      <p class="mt-4 text-slate-500 text-xs">
        © <?php echo date("Y"); ?> Real-Tech Services Limited. All rights reserved.
      </p>
    </div>
  </footer>

  <script>
    // Quantity Controls
    const quantityInput = document.querySelector('input[name="qty"]');
    const quantityDisplay = document.getElementById('quantity-display');
    const sidebarQuantity = document.getElementById('sidebar-quantity');
    const totalAmount = document.getElementById('total-amount');
    const sidebarTotal = document.getElementById('sidebar-total');
    const unitPrice = <?php echo $product['price']; ?>;
    
    // Update quantity
    function updateQuantity(value) {
      let newQty = parseInt(value);
      if (isNaN(newQty) || newQty < 1) newQty = 1;
      
      quantityInput.value = newQty;
      quantityDisplay.textContent = newQty;
      sidebarQuantity.textContent = newQty;
      
      const total = newQty * unitPrice;
      totalAmount.textContent = 'RWF ' + total.toLocaleString();
      sidebarTotal.textContent = 'RWF ' + total.toLocaleString();
    }
    
    // Increment button
    document.querySelector('.increment').addEventListener('click', () => {
      updateQuantity(parseInt(quantityInput.value) + 1);
    });
    
    // Decrement button
    document.querySelector('.decrement').addEventListener('click', () => {
      updateQuantity(parseInt(quantityInput.value) - 1);
    });
    
    // Input change
    quantityInput.addEventListener('change', (e) => {
      updateQuantity(e.target.value);
    });
    
    // Input keyup for real-time updates
    quantityInput.addEventListener('input', (e) => {
      updateQuantity(e.target.value);
    });
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
      form.addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
          if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-500');
            
            // Add error message
            if (!field.nextElementSibling?.classList.contains('error-message')) {
              const errorMsg = document.createElement('p');
              errorMsg.className = 'error-message text-red-500 text-sm mt-1';
              errorMsg.textContent = 'This field is required';
              field.parentNode.appendChild(errorMsg);
            }
          } else {
            field.classList.remove('border-red-500');
            const errorMsg = field.parentNode.querySelector('.error-message');
            if (errorMsg) errorMsg.remove();
          }
        });
        
        // Check terms
        const termsCheckbox = document.getElementById('terms');
        if (termsCheckbox && !termsCheckbox.checked) {
          isValid = false;
          alert('Please accept the terms and conditions to proceed.');
        }
        
        if (!isValid) {
          e.preventDefault();
          // Scroll to first error
          const firstError = this.querySelector('.border-red-500');
          if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
          }
        }
      });
    }
    
    // Format phone number
    const phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput) {
      phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Add country code if starting with 0 or 7
        if (value.length > 0) {
          if (value.startsWith('0')) {
            value = '+250' + value.substring(1);
          } else if (value.startsWith('7') && !value.startsWith('+')) {
            value = '+250' + value;
          } else if (!value.startsWith('+')) {
            value = '+250' + value;
          }
        }
        
        // Format with spaces
        if (value.length > 3) {
          value = value.substring(0, 4) + ' ' + value.substring(4);
        }
        if (value.length > 8) {
          value = value.substring(0, 8) + ' ' + value.substring(8);
        }
        if (value.length > 12) {
          value = value.substring(0, 12) + ' ' + value.substring(12);
        }
        
        e.target.value = value.substring(0, 16); // Limit length
      });
    }
    
    // Auto-save form data to localStorage (in case of page refresh)
    if (!<?php echo $msg ? 'true' : 'false'; ?>) {
      const formFields = document.querySelectorAll('input, textarea');
      
      // Load saved data
      formFields.forEach(field => {
        const savedValue = localStorage.getItem(`order_form_${field.name}`);
        if (savedValue && field.value === '') {
          field.value = savedValue;
          if (field.name === 'qty') updateQuantity(savedValue);
        }
      });
      
      // Save on input
      formFields.forEach(field => {
        field.addEventListener('input', (e) => {
          localStorage.setItem(`order_form_${e.target.name}`, e.target.value);
        });
      });
      
      // Clear on successful submission
      form.addEventListener('submit', () => {
        formFields.forEach(field => {
          localStorage.removeItem(`order_form_${field.name}`);
        });
      });
    }
    
    // Add animation to success page
    if (<?php echo $msg ? 'true' : 'false'; ?>) {
      setTimeout(() => {
        const checkmark = document.querySelector('.check-icon');
        if (checkmark) {
          checkmark.style.transform = 'scale(1.1)';
          checkmark.style.transition = 'transform 0.3s ease';
          
          setTimeout(() => {
            checkmark.style.transform = 'scale(1)';
          }, 300);
        }
      }, 500);
    }
  </script>
</body>
</html>