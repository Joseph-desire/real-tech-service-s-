<?php
require_once __DIR__ . "/../config/db.php";

$services  = db_query("SELECT * FROM services ORDER BY created_at DESC");
$products  = db_query("SELECT * FROM products ORDER BY created_at DESC");
$portfolio = db_query("SELECT * FROM portfolio_items ORDER BY created_at DESC");
$team      = db_query("SELECT * FROM team_members ORDER BY created_at DESC");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Real-Tech Services Limited</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* Enhanced Fade Animations */
.fade-up {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}
.fade-up.show {
    opacity: 1;
    transform: translateY(0);
}

/* Slow number counter */
.counter {
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 10px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
    background: #4f46e5;
    border-radius: 5px;
}
::-webkit-scrollbar-thumb:hover {
    background: #4338ca;
}

/* Smooth transitions */
* {
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

/* Mobile menu */
.mobile-menu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease-out;
}
.mobile-menu.open {
    max-height: 500px;
}
</style>

</head>
<body class="bg-slate-50 text-slate-900">

<!-- ENHANCED NAVBAR WITH MOBILE MENU -->
<header class="bg-white/90 backdrop-blur-md border-b sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="font-black text-2xl tracking-tight text-indigo-700 flex items-center gap-2">
            <i class="fas fa-microchip"></i>
            <span>Real-Tech Services Ltd</span>
        </div>
        
        <!-- Desktop Navigation -->
        <nav class="hidden md:flex gap-6 text-sm font-semibold">
            <a href="#home" class="hover:text-indigo-600 flex items-center gap-1">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="#services" class="hover:text-indigo-600 flex items-center gap-1">
                <i class="fas fa-cogs"></i> Services
            </a>
            <a href="#products" class="hover:text-indigo-600 flex items-center gap-1">
                <i class="fas fa-box"></i> Products
            </a>
            <a href="#about" class="hover:text-indigo-600 flex items-center gap-1">
                <i class="fas fa-info-circle"></i> About
            </a>
            <a href="#portfolio" class="hover:text-indigo-600 flex items-center gap-1">
                <i class="fas fa-briefcase"></i> Portfolio
            </a>
            <a href="#team" class="hover:text-indigo-600 flex items-center gap-1">
                <i class="fas fa-users"></i> Team
            </a>
            <a href="#contact" class="hover:text-indigo-600 flex items-center gap-1">
                <i class="fas fa-envelope"></i> Contact
            </a>
        </nav>

        <div class="flex items-center gap-4">
            <a class="hidden md:inline text-sm px-4 py-2 rounded-lg bg-indigo-600 text-white shadow hover:bg-indigo-700 transition-transform hover:scale-105"
               href="/realtech/admin/login.php">
                <i class="fas fa-user-shield mr-2"></i>Admin
            </a>
            
            <!-- Mobile menu button -->
            <button id="mobileMenuButton" class="md:hidden text-slate-700 text-xl">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="mobile-menu md:hidden bg-white border-t">
        <div class="flex flex-col py-4 px-4 space-y-3">
            <a href="#home" class="py-2 px-4 hover:bg-indigo-50 rounded-lg flex items-center gap-2">
                <i class="fas fa-home w-6"></i> Home
            </a>
            <a href="#services" class="py-2 px-4 hover:bg-indigo-50 rounded-lg flex items-center gap-2">
                <i class="fas fa-cogs w-6"></i> Services
            </a>
            <a href="#products" class="py-2 px-4 hover:bg-indigo-50 rounded-lg flex items-center gap-2">
                <i class="fas fa-box w-6"></i> Products
            </a>
            <a href="#about" class="py-2 px-4 hover:bg-indigo-50 rounded-lg flex items-center gap-2">
                <i class="fas fa-info-circle w-6"></i> About
            </a>
            <a href="#portfolio" class="py-2 px-4 hover:bg-indigo-50 rounded-lg flex items-center gap-2">
                <i class="fas fa-briefcase w-6"></i> Portfolio
            </a>
            <a href="#team" class="py-2 px-4 hover:bg-indigo-50 rounded-lg flex items-center gap-2">
                <i class="fas fa-users w-6"></i> Team
            </a>
            <a href="#contact" class="py-2 px-4 hover:bg-indigo-50 rounded-lg flex items-center gap-2">
                <i class="fas fa-envelope w-6"></i> Contact
            </a>
            <a href="/realtech/admin/login.php" class="py-2 px-4 bg-indigo-600 text-white rounded-lg flex items-center justify-center gap-2 mt-2">
                <i class="fas fa-user-shield"></i> Admin Panel
            </a>
        </div>
    </div>
</header>

<!-- HERO -->
<section id="home" class="bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 py-20 fade-up">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-10">
            <div class="lg:w-2/3">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight">
                    Welcome to the Future of <span class="text-indigo-300">Technology</span>
                </h1>
                
                <p class="mt-6 text-lg md:text-xl text-indigo-100 max-w-3xl">
                    Innovating tomorrow's technology solutions—electrical installations, electronics services,
                    CCTV systems, and advanced networking solutions for businesses and homes.
                </p>

                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="#services"
                       class="px-8 py-4 rounded-xl bg-white text-indigo-800 text-lg font-semibold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                       <i class="fas fa-rocket"></i> Explore Services
                    </a>

                    <a href="#contact"
                       class="px-8 py-4 rounded-xl bg-indigo-600 text-white text-lg font-semibold shadow-lg hover:scale-105 transition-all flex items-center gap-2">
                       <i class="fas fa-phone-alt"></i> Contact Us
                    </a>
                </div>
            </div>
            
            <div class="lg:w-1/3">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                    <h3 class="text-2xl font-bold mb-4">Why Choose Us?</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>15+ Years Experience</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>Certified Professionals</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>24/7 Support</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <span>Quality Guarantee</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section id="services" class="max-w-7xl mx-auto px-4 py-16">
    <div class="text-center mb-12">
        <h2 class="text-4xl font-black fade-up">Our <span class="text-indigo-700">Services</span></h2>
        <p class="text-slate-600 mt-4 max-w-2xl mx-auto fade-up">
            All service data is dynamically uploaded by the admin. We provide comprehensive technology solutions.
        </p>
    </div>

    <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php while($s = mysql_fetch_assoc($services)): ?>
        <div class="fade-up rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden hover:-translate-y-2">
            <div class="relative overflow-hidden">
                <img class="h-52 w-full object-cover transition-transform duration-500 hover:scale-110"
                     src="/realtech/<?php echo htmlspecialchars($s['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($s['title']); ?>" />
                <div class="absolute top-4 right-4 bg-indigo-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                    Service
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-cog text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($s['title']); ?></h3>
                </div>
                <p class="text-slate-600"><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                <a href="#contact" class="mt-4 inline-flex items-center gap-2 text-indigo-600 font-semibold hover:text-indigo-800">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- PRODUCTS -->
<section id="products" class="bg-gradient-to-b from-white to-slate-50 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black fade-up">Our <span class="text-indigo-700">Products</span></h2>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto fade-up">
                Shop our latest electronic and networking products with competitive pricing.
            </p>
        </div>

        <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($p = mysql_fetch_assoc($products)): ?>
            <div class="fade-up rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden hover:-translate-y-2">
                <div class="relative overflow-hidden">
                    <img class="h-52 w-full object-cover transition-transform duration-500 hover:scale-110"
                         src="/realtech/<?php echo htmlspecialchars($p['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($p['name']); ?>" />
                    <div class="absolute top-4 right-4 bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        In Stock
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($p['name']); ?></h3>
                    <p class="mt-2 text-slate-600 text-sm"><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
                    
                    <div class="mt-4 flex items-center justify-between">
                        <div>
                            <div class="text-indigo-700 font-extrabold text-2xl">RWF <?php echo number_format($p['price'], 0); ?></div>
                            <div class="text-sm text-slate-500">+ VAT if applicable</div>
                        </div>
                        
                        <a href="/realtech/public/order.php?product_id=<?php echo (int)$p['id']; ?>"
                           class="px-6 py-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all flex items-center gap-2 hover:scale-105">
                           <i class="fas fa-shopping-cart"></i> Order Now
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section id="about" class="bg-gradient-to-b from-slate-100 to-white py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black fade-up">About <span class="text-indigo-700">Real-Tech</span></h2>
        </div>

        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="fade-up">
                <h3 class="text-3xl font-bold text-slate-800 mb-6">Transforming Ideas into Reality Since 2009</h3>
                <p class="text-slate-700 text-lg mb-6">
                    Real-Tech Services and Solutions Limited, founded in <b>2009</b>, is committed to transforming ideas into
                    innovative technology solutions. With over <b>15 years</b> of industry experience, we deliver
                    reliable, cutting-edge services that empower businesses and improve everyday life.
                </p>
                
                <div class="bg-indigo-50 border-l-4 border-indigo-600 p-4 rounded-r-lg">
                    <p class="text-slate-700 italic">
                        "Our mission is to bridge the gap between technology and practical application, 
                        delivering solutions that truly make a difference."
                    </p>
                </div>
            </div>
            
            <div class="fade-up">
                <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                     alt="About Real-Tech" 
                     class="rounded-2xl shadow-2xl w-full h-96 object-cover">
            </div>
        </div>

        <!-- METRICS / COUNTERS -->
        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="fade-up bg-white p-8 rounded-2xl shadow-lg">
                <div class="text-5xl font-black text-indigo-700 counter mb-2" data-target="15">0</div>
                <p class="font-semibold text-slate-700">Years Experience</p>
            </div>
            <div class="fade-up bg-white p-8 rounded-2xl shadow-lg">
                <div class="text-5xl font-black text-indigo-700 counter mb-2" data-target="500">0</div>
                <p class="font-semibold text-slate-700">Projects Completed</p>
            </div>
            <div class="fade-up bg-white p-8 rounded-2xl shadow-lg">
                <div class="text-5xl font-black text-indigo-700 counter mb-2" data-target="200">0</div>
                <p class="font-semibold text-slate-700">Happy Clients</p>
            </div>
            <div class="fade-up bg-white p-8 rounded-2xl shadow-lg">
                <div class="text-5xl font-black text-indigo-700 counter mb-2" data-target="98">0</div>
                <p class="font-semibold text-slate-700">Success Rate (%)</p>
            </div>
        </div>

        <!-- VALUES / VISION -->
        <div class="mt-16 grid md:grid-cols-3 gap-8">
            <div class="fade-up p-8 bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-lg hover:shadow-xl transition-all border border-indigo-100">
                <div class="w-14 h-14 rounded-xl bg-indigo-600 flex items-center justify-center mb-6">
                    <i class="fas fa-eye text-2xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Our Vision</h3>
                <p class="text-slate-600">
                    To become the global leader in technology innovation, setting the benchmark for excellence 
                    in electrical, electronics, and networking solutions.
                </p>
            </div>

            <div class="fade-up p-8 bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-lg hover:shadow-xl transition-all border border-indigo-100">
                <div class="w-14 h-14 rounded-xl bg-indigo-600 flex items-center justify-center mb-6">
                    <i class="fas fa-bullseye text-2xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Our Mission</h3>
                <p class="text-slate-600">
                    Delivering cutting-edge solutions that drive business growth, enhance security, 
                    and transform lives through innovative technology.
                </p>
            </div>

            <div class="fade-up p-8 bg-gradient-to-br from-indigo-50 to-white rounded-2xl shadow-lg hover:shadow-xl transition-all border border-indigo-100">
                <div class="w-14 h-14 rounded-xl bg-indigo-600 flex items-center justify-center mb-6">
                    <i class="fas fa-heart text-2xl text-white"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Our Values</h3>
                <p class="text-slate-600">
                    Innovation, Excellence, Integrity, Customer Focus, and Continuous Improvement 
                    are the pillars of our success.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- PORTFOLIO -->
<section id="portfolio" class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black fade-up">Our <span class="text-indigo-700">Portfolio</span></h2>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto fade-up">
                Completed projects uploaded by admin. Showcasing our expertise in real-world applications.
            </p>
        </div>

        <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($t = mysql_fetch_assoc($portfolio)): ?>
            <div class="fade-up rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden hover:-translate-y-2 group">
                <div class="relative overflow-hidden">
                    <img class="h-52 w-full object-cover transition-transform duration-500 group-hover:scale-110"
                         src="/realtech/<?php echo htmlspecialchars($t['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($t['title']); ?>" />
                    <div class="absolute inset-0 bg-indigo-600/0 group-hover:bg-indigo-600/20 transition-all duration-300"></div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($t['title']); ?></h3>
                    <p class="mt-2 text-slate-600 text-sm"><?php echo nl2br(htmlspecialchars($t['description'])); ?></p>
                    <div class="mt-4 flex items-center gap-2 text-sm text-indigo-600">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Completed Project</span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- TEAM -->
<section id="team" class="bg-gradient-to-b from-slate-50 to-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black fade-up">Meet Our <span class="text-indigo-700">Team</span></h2>
            <p class="text-slate-600 mt-4 max-w-2xl mx-auto fade-up">
                The talented professionals behind Real-Tech's success story.
            </p>
        </div>

        <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php while($m = mysql_fetch_assoc($team)): ?>
            <div class="fade-up rounded-2xl border border-slate-200 bg-white shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden hover:-translate-y-2 text-center">
                <div class="relative pt-8">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-white shadow-lg">
                        <img class="w-full h-full object-cover"
                             src="/realtech/<?php echo htmlspecialchars($m['image_path']); ?>" 
                             alt="<?php echo htmlspecialchars($m['name']); ?>" />
                    </div>
                </div>
                <div class="p-6 pt-12">
                    <h3 class="text-xl font-bold text-slate-800"><?php echo htmlspecialchars($m['name']); ?></h3>
                    <div class="text-indigo-700 font-semibold mt-1"><?php echo htmlspecialchars($m['role']); ?></div>
                    <p class="mt-4 text-slate-600 text-sm"><?php echo nl2br(htmlspecialchars($m['bio'])); ?></p>
                    
                    <div class="mt-6 flex justify-center gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-indigo-600 hover:text-white transition-colors">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-indigo-600 hover:text-white transition-colors">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-700 hover:bg-indigo-600 hover:text-white transition-colors">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- CONTACT SECTION -->
<section id="contact" class="bg-gradient-to-br from-indigo-700 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-black fade-up">Get In <span class="text-indigo-300">Touch</span></h2>
            <p class="text-indigo-100 mt-4 max-w-2xl mx-auto fade-up">
                Have a project in mind? Contact us for a free consultation.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12">
            <div class="fade-up">
                <h3 class="text-2xl font-bold mb-6">Contact Information</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-600/30 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Our Location</h4>
                            <p class="text-indigo-100">Kigali, Rwanda</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-600/30 flex items-center justify-center">
                            <i class="fas fa-phone-alt text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Phone Number</h4>
                            <p class="text-indigo-100">+250 788 123 456</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-600/30 flex items-center justify-center">
                            <i class="fas fa-envelope text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Email Address</h4>
                            <p class="text-indigo-100">info@realtech.rw</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-indigo-600/30 flex items-center justify-center">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">Working Hours</h4>
                            <p class="text-indigo-100">Mon - Fri: 8:00 AM - 6:00 PM</p>
                            <p class="text-indigo-100">Sat: 9:00 AM - 1:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="fade-up">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
                    <h3 class="text-2xl font-bold mb-6">Send Us a Message</h3>
                    <form class="space-y-4">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <input type="text" placeholder="Your Name" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <input type="email" placeholder="Your Email" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        </div>
                        <input type="text" placeholder="Subject" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <textarea placeholder="Your Message" rows="4" class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
                        <button type="submit" class="w-full px-6 py-4 rounded-lg bg-white text-indigo-700 font-bold hover:bg-indigo-100 transition-colors">
                            Send Message <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ENHANCED FOOTER -->
<footer class="bg-slate-900 text-white pt-12 pb-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Company Info -->
            <div class="fade-up">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <i class="fas fa-microchip text-xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold">Real-Tech</h3>
                </div>
                <p class="text-slate-300 mb-6">
                    Leading technology solutions provider since 2009. 
                    We deliver excellence in electrical, electronics, and networking services.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-indigo-600 transition-colors">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-indigo-600 transition-colors">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-indigo-600 transition-colors">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white hover:bg-indigo-600 transition-colors">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="fade-up">
                <h4 class="text-xl font-bold mb-6">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="#home" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-chevron-right text-xs"></i> Home
                    </a></li>
                    <li><a href="#services" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-chevron-right text-xs"></i> Services
                    </a></li>
                    <li><a href="#products" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-chevron-right text-xs"></i> Products
                    </a></li>
                    <li><a href="#about" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-chevron-right text-xs"></i> About Us
                    </a></li>
                    <li><a href="#contact" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-chevron-right text-xs"></i> Contact
                    </a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="fade-up">
                <h4 class="text-xl font-bold mb-6">Our Services</h4>
                <ul class="space-y-3">
                    <li><a href="#" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-bolt text-sm"></i> Electrical Installations
                    </a></li>
                    <li><a href="#" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-camera text-sm"></i> CCTV Systems
                    </a></li>
                    <li><a href="#" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-network-wired text-sm"></i> Networking Solutions
                    </a></li>
                    <li><a href="#" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-shield-alt text-sm"></i> Security Systems
                    </a></li>
                    <li><a href="#" class="text-slate-300 hover:text-white transition-colors flex items-center gap-2">
                        <i class="fas fa-solar-panel text-sm"></i> Solar Solutions
                    </a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="fade-up">
                <h4 class="text-xl font-bold mb-6">Stay Updated</h4>
                <p class="text-slate-300 mb-4">
                    Subscribe to our newsletter for the latest updates and offers.
                </p>
                <form class="space-y-3">
                    <input type="email" placeholder="Your Email" class="w-full px-4 py-3 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="w-full px-6 py-3 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-t border-slate-800 pt-8 mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-slate-400 text-sm">
                    © <?php echo date("Y"); ?> Real-Tech Services and Solutions Limited. All rights reserved.
                </div>
                
                <div class="flex items-center gap-6 text-sm">
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-slate-400 hover:text-white transition-colors">Cookie Policy</a>
                    <a href="/realtech/admin/login.php" class="text-indigo-400 hover:text-indigo-300 transition-colors flex items-center gap-1">
                        <i class="fas fa-user-shield"></i> Admin
                    </a>
                </div>
            </div>
            
            <div class="text-center mt-6 text-slate-500 text-xs">
                <p>Registered in Rwanda | VAT No: 123456789 | Business License: 987654321</p>
                <p class="mt-2">
                    <i class="fas fa-heart text-red-500"></i> Made with passion for technology innovation
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top Button -->
<button id="backToTop" class="fixed bottom-8 right-8 w-12 h-12 rounded-full bg-indigo-600 text-white shadow-lg hover:bg-indigo-700 transition-all opacity-0 invisible z-40 flex items-center justify-center">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- JS: Enhanced Animations + Mobile Menu + Back to Top -->
<script>
// Fade-up effect when elements enter viewport
const fadeEls = document.querySelectorAll(".fade-up");
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add("show");
        }
    });
}, {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px"
});
fadeEls.forEach(el => observer.observe(el));

// Counter animation with delay
document.querySelectorAll('.counter').forEach(counter => {
    let target = +counter.getAttribute('data-target');
    let count = 0;
    let speed = 2000; // 2 seconds
    
    let increment = target / (speed / 16); // 60fps
    let interval = setInterval(() => {
        count += increment;
        if (count >= target) {
            counter.textContent = target;
            clearInterval(interval);
        } else {
            counter.textContent = Math.floor(count);
        }
    }, 16);
});

// Mobile Menu Toggle
const mobileMenuButton = document.getElementById('mobileMenuButton');
const mobileMenu = document.getElementById('mobileMenu');

if (mobileMenuButton && mobileMenu) {
    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('open');
        const icon = mobileMenuButton.querySelector('i');
        if (mobileMenu.classList.contains('open')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
}

// Close mobile menu when clicking a link
document.querySelectorAll('#mobileMenu a').forEach(link => {
    link.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        const icon = mobileMenuButton.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
    });
});

// Back to Top Button
const backToTop = document.getElementById('backToTop');
window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTop.classList.remove('opacity-0', 'invisible');
        backToTop.classList.add('opacity-100', 'visible');
    } else {
        backToTop.classList.remove('opacity-100', 'visible');
        backToTop.classList.add('opacity-0', 'invisible');
    }
});

backToTop.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        if (this.getAttribute('href') === '#') return;
        
        e.preventDefault();
        const targetId = this.getAttribute('href');
        if (targetId === '#home') {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            return;
        }
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            const headerHeight = document.querySelector('header').offsetHeight;
            const targetPosition = targetElement.offsetTop - headerHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// Form submission (placeholder)
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            alert('Thank you! Your message has been sent. We will get back to you soon.');
            this.reset();
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 1500);
    });
});
</script>

</body>
</html>