

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaveWise - Smart Personal Finance Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#10B981',
                        dark: '#1F2937',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    </style>
</head>
<body class="font-sans bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <a href="#" class="text-2xl font-bold text-primary flex items-center">
                    <i class="fas fa-wallet mr-2"></i>SaveWise
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-dark hover:text-primary transition">Features</a>
                    <a href="#how-it-works" class="text-dark hover:text-primary transition">How It Works</a>
                    <a href="#testimonials" class="text-dark hover:text-primary transition">Testimonials</a>
                    <a href="signin.php" class="text-dark hover:text-primary transition">signin</a>
                    <a href="signup.php" class="bg-primary text-white px-5 py-2 rounded-lg hover:bg-primary/90 transition">Get Started</a>
                </div>
                
                <button class="md:hidden focus:outline-none" id="mobile-menu-button">
                    <i class="fas fa-bars text-2xl text-dark"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div class="md:hidden hidden mt-4 pb-2" id="mobile-menu">
                <a href="#features" class="block py-3 px-2 hover:bg-gray-100 rounded">Features</a>
                <a href="#how-it-works" class="block py-3 px-2 hover:bg-gray-100 rounded">How It Works</a>
                <a href="#testimonials" class="block py-3 px-2 hover:bg-gray-100 rounded">Testimonials</a>
                <a href="signin.php" class="block py-3 px-2 hover:bg-gray-100 rounded">signin</a>
                <a href="signup.php" class="block bg-primary text-white py-3 px-4 rounded mt-2 text-center">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-primary to-primary/90 text-white py-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-32">
                <div class="lg:w-1/2">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">Take Control of Your Finances</h1>
                    <p class="text-xl mb-8 text-white/90">SaveWise helps you track spending, set budgets, and reach your financial goals faster.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="signup.php" class="bg-white text-primary px-6 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition text-center">
                            Start Free Trial
                        </a>
                        <a href="#how-it-works" class="border-2 border-white text-white px-6 py-4 rounded-lg text-lg font-semibold hover:bg-white/10 transition text-center">
                            See How It Works
                        </a>
                    </div>
                </div>
                <div class="  mt-10 lg:mt-0">
                    <img src="https://i.pinimg.com/736x/30/0d/e7/300de7ec07c5891471474bf95bd0e6ca.jpg" 
                         alt="Finance dashboard on phone and laptop" 
                         width="400" height="300"
                         class="rounded-xl shadow-2xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="p-6">
                    <div class="text-4xl font-bold text-primary mb-2">50K+</div>
                    <div class="text-gray-600">Active Users</div>
                </div>
                <div class="p-6">
                    <div class="text-4xl font-bold text-secondary mb-2">$100M+</div>
                    <div class="text-gray-600">Tracked Monthly</div>
                </div>
                <div class="p-6">
                    <div class="text-4xl font-bold text-primary mb-2">4.9★</div>
                    <div class="text-gray-600">Average Rating</div>
                </div>
                <div class="p-6">
                    <div class="text-4xl font-bold text-secondary mb-2">24/7</div>
                    <div class="text-gray-600">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4 text-dark">Powerful Features</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Everything you need to manage your money wisely</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-primary text-4xl mb-5">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Expense Tracking</h3>
                    <p class="text-gray-600">Automatically categorize and visualize your spending patterns with beautiful charts.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-secondary text-4xl mb-5">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Goal Setting</h3>
                    <p class="text-gray-600">Set savings targets and track your progress toward financial milestones.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-primary text-4xl mb-5">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">AI Insights</h3>
                    <p class="text-gray-600">Get personalized recommendations to optimize your spending and savings.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-secondary text-4xl mb-5">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Mobile Sync</h3>
                    <p class="text-gray-600">Access your finances anywhere with our iOS and Android apps.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-primary text-4xl mb-5">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Bank-Level Security</h3>
                    <p class="text-gray-600">256-bit encryption protects your financial data at all times.</p>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="text-secondary text-4xl mb-5">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Export Reports</h3>
                    <p class="text-gray-600">Generate PDF or CSV reports for taxes or financial planning.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4 text-dark">How SaveWise Works</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Get started in just a few simple steps</p>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-12 items-center">
                <div class="lg:w-1/2">
                    <div class="space-y-8">
                        <div class="flex items-start gap-6">
                            <div class="bg-primary/10 text-primary rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold">1</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Connect Your Accounts</h3>
                                <p class="text-gray-600">Securely link your bank, credit cards, and investment accounts in minutes.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-6">
                            <div class="bg-secondary/10 text-secondary rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold">2</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Set Your Budget</h3>
                                <p class="text-gray-600">Create custom spending categories and monthly budget limits.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-6">
                            <div class="bg-primary/10 text-primary rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold">3</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Track & Optimize</h3>
                                <p class="text-gray-600">Monitor your spending and get alerts when you're approaching budget limits.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-6">
                            <div class="bg-secondary/10 text-secondary rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold">4</span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold mb-2">Achieve Your Goals</h3>
                                <p class="text-gray-600">Watch your savings grow and celebrate your financial milestones.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:w-1/2">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="Woman using SaveWise app on her phone" 
                         class="rounded-xl shadow-lg w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4 text-dark">What Our Users Say</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Join thousands who have transformed their finances</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-xl shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">"SaveWise helped me identify $200/month in unnecessary subscriptions I'd forgotten about. Paid for itself in the first week!"</p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah J." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Sarah J.</h4>
                            <p class="text-gray-500 text-sm">Small Business Owner</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">"As a freelancer with irregular income, the budgeting tools have been a game-changer for my financial stability."</p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Michael T." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Michael T.</h4>
                            <p class="text-gray-500 text-sm">Graphic Designer</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-8 rounded-xl shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="text-yellow-400 mr-1">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6">"We paid off $15,000 in credit card debt in 18 months using SaveWise's goal tracking and spending alerts."</p>
                    <div class="flex items-center">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Lisa & Mark R." class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold">Lisa & Mark R.</h4>
                            <p class="text-gray-500 text-sm">Married Couple</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 bg-primary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Ready to Transform Your Finances?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Join over 50,000 users who are saving smarter with SaveWise</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="signup.php" class="bg-white text-primary px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                    Start Your Free Trial
                </a>
                <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white/10 transition">
                    Learn More
                </a>
            </div>
            <p class="mt-6 text-white/80">No credit card required • Cancel anytime</p>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4 text-dark">Frequently Asked Questions</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Everything you need to know</p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <div class="mb-6 border-b pb-6">
                    <h3 class="text-xl font-bold mb-3">Is my financial data secure?</h3>
                    <p class="text-gray-600">Absolutely. We use bank-level 256-bit encryption and never store your banking credentials. Your data is protected with the same security standards as major financial institutions.</p>
                </div>
                
                <div class="mb-6 border-b pb-6">
                    <h3 class="text-xl font-bold mb-3">What banks do you support?</h3>
                    <p class="text-gray-600">SaveWise connects with over 10,000 financial institutions in North America, including all major banks, credit unions, and brokerages. We're constantly adding new partners.</p>
                </div>
                
                <div class="mb-6 border-b pb-6">
                    <h3 class="text-xl font-bold mb-3">How much does it cost?</h3>
                    <p class="text-gray-600">We offer a free version with basic features. Our premium plan starts at $9.99/month with a 30-day free trial. Annual plans save you 20%.</p>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-xl font-bold mb-3">Can I use SaveWise outside the US?</h3>
                    <p class="text-gray-600">Currently we fully support US and Canadian financial institutions. We plan to expand to Europe and other regions in 2024.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 bg-dark text-white">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-12 mb-12">
                <div>
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-wallet mr-2"></i> SaveWise
                    </h3>
                    <p class="text-gray-400">Smart personal finance tracking for everyone.</p>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Features</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Pricing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Mobile Apps</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Integrations</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Guides</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Webinars</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Careers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Privacy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Terms</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">© 2023 SaveWise. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile menu toggle script -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>