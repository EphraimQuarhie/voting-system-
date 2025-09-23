<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex-shrink-0">
                    <a href="index.html" class="text-indigo-600 font-bold text-2xl">VoteSphere</a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="index.php" class="text-gray-800 hover:text-indigo-600 transition duration-200 font-semibold">Home</a>
                    <a href="register.php" class="text-gray-800 hover:text-indigo-600 transition duration-200 font-semibold">Register</a>
                    <a href="login.php" class="text-gray-800 hover:text-indigo-600 transition duration-200 font-semibold">Login</a>
                    <a href="admin.php" class="text-gray-800 hover:text-indigo-600 transition duration-200 font-semibold">Admin Panel</a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 rounded-md p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="index.php" class="block rounded-md px-3 py-2 text-gray-800 hover:bg-gray-100 hover:text-indigo-600 font-semibold">Home</a>
                <a href="register.php" class="block rounded-md px-3 py-2 text-gray-800 hover:bg-gray-100 hover:text-indigo-600 font-semibold">Register</a>
                <a href="login.php" class="block rounded-md px-3 py-2 text-gray-800 hover:bg-gray-100 hover:text-indigo-600 font-semibold">Login</a>
                <a href="admin.php" class="block rounded-md px-3 py-2 text-gray-800 hover:bg-gray-100 hover:text-indigo-600 font-semibold">Admin Panel</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="bg-white py-20 md:py-32 flex-grow flex items-center">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-4">
                Secure & Transparent <span class="text-indigo-600">Online Voting</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Empower your community with a modern, reliable, and trustworthy platform for all your elections. From school councils to community boards, our system ensures every vote counts.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="register.php" class="bg-indigo-600 text-white font-bold py-3 px-8 rounded-full shadow-lg hover:bg-indigo-700 transition duration-300 transform hover:scale-105">
                    Get Started
                </a>
                <a href="login.php" class="bg-transparent text-indigo-600 font-bold py-3 px-8 rounded-full border border-indigo-600 hover:bg-indigo-100 transition duration-300 transform hover:scale-105">
                    Log In
                </a>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-3xl shadow-lg text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Secure Registration</h3>
                    <p class="text-gray-500">
                        Voters can easily register and log in with their credentials. Our system ensures every user is authenticated for a fair voting process.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-3xl shadow-lg text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5L15 10.5M12 7.5L9 10.5M12 7.5L12 18M15 18H9M12 18L12 21" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Transparent Elections</h3>
                    <p class="text-gray-500">
                        Admins can create new elections and add candidates with full control. The system ensures transparency by tracking votes securely.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-3xl shadow-lg text-center transform hover:scale-105 transition-transform duration-300">
                    <div class="inline-flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 text-indigo-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13V12a9 9 0 019-9h0a9 9 0 019 9v1m-6 3l3-3m0 0l3 3m-3-3l-3 3" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Real-time Results</h3>
                    <p class="text-gray-500">
                        View live election results as votes come in. Our dynamic charts and tables provide instant insights into the voting process.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call-to-Action Section -->
    <section class="bg-indigo-600 py-16 md:py-24">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Create Your Election?</h2>
            <p class="text-indigo-200 mb-8 max-w-2xl mx-auto">
                Whether you're organizing a small community poll or a large-scale election, VoteSphere is the perfect platform to get started.
            </p>
                <a href="register.php" class="bg-white text-indigo-600 font-bold py-3 px-8 rounded-full shadow-lg hover:bg-gray-100 transition duration-300 transform hover:scale-105">
                Register Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2023 VoteSphere. All rights reserved.</p>
        </div>
    </footer>

    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
