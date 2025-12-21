<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Diva House Beauty</title>
    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="diva house beauty">
    <meta name="author" content="izere moubarak">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/icons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/icons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/images/icons/site.html') }}">
    <link rel="mask-icon" href="{{ asset('assets/images/icons/safari-pinned-tab.svg') }}" color="#666666">
    <link rel="shortcut icon" href="{{ asset('assets/images/icons/favicon.ico') }}">
    <meta name="apple-mobile-web-app-title" content="diva house beauty">
    <meta name="application-name" content="diva house beauty">
    <meta name="msapplication-TileColor" content="#cc9966">
    <meta name="msapplication-config" content="{{ asset('assets/images/icons/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">
    <!-- Plugins CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/owl-carousel/owl.carousel.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/magnific-popup/magnific-popup.css') }}">
    <!-- Main CSS File -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/nouislider/nouislider.css') }}">
</head>


<body>
    <div class="page-wrapper">
        <header class="header">
            <div class="header-top">
                <div class="container">
                    <div class="header-left">
                        <ul class="">
                        <li><a href="tel:0780159059"><i class="icon-phone"></i>Call: +2507 8015 9059</a></li>
                        <ul>
                    </div><!-- End .header-left -->
            
                    <div class="header-right">
                        <ul class="top-menu">
                            <li>
                                <a href="#">Links</a>
                                <ul class="menus">
                                    
                                    <li><a href="about.html">About Us</a></li>
                                    <li><a href="contact.html">Contact Us</a></li>
                                    @guest
                                        <!-- Show Sign In / Sign Up if the user is not logged in -->
                                        <li class="login">
                                            <a href="#signin-modal" data-toggle="modal"> <i class="icon-user"></i>Sign in / Sign up</a>
                                        </li>
                                    @else
                                        <!-- Show User Name if the user is logged in -->
                                        <li class="user">
                                            <a href="#">Welcome, {{ Auth::user()->name }}</a>
                                            <ul class="menus">
                                                <li>
                                                    <a href="{{ route('customer.dashboard') }}">Dashboard</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('logout') }}" 
                                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                        Logout
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- Logout Form (Hidden) -->
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    @endguest
                                </ul>
                            </li>
                        </ul><!-- End .top-menu -->
                    </div><!-- End .header-right -->
                    
                                        
                                       
                </div><!-- End .container -->
            </div>

            <div class="header-middle sticky-header">
                <div class="container">
                    <div class="header-left">
                        <button class="mobile-menu-toggler">
                            <span class="sr-only">Toggle mobile menu</span>
                            <i class="icon-bars"></i>
                        </button>

                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ asset('assets/images/demos/demo-14/logo.png') }}" alt="Molla Logo" width="105" height="25">
                        </a>
                        

                        <nav class="main-nav">
                            <ul class="menu sf-arrows">
                                <li class="megamenu-container">
                                    <a href="index.html" class="sf-with-ul">Home</a>
                                <li class="active">
                                    <a href="category.html" class="sf-with-ul">Shop</a><!-- End .megamenu megamenu-md -->
                                </li>
                                <li>
                                    <a href="product.html" class="sf-with-ul">Services</a>

                                    <div class="megamenu megamenu-sm">
                                        <div class="row no-gutters">
                                            <div class="col-md-6">
                                                <div class="menu-col">
                                                    <div class="menu-title">Product Details</div><!-- End .menu-title -->
                                                    <ul>
                                                        <li><a href="product.html">Default</a></li>
                                                        <li><a href="product-centered.html">Centered</a></li>
                                                        <li><a href="product-extended.html"><span>Extended Info<span class="tip tip-new">New</span></span></a></li>
                                                        <li><a href="product-gallery.html">Gallery</a></li>
                                                        <li><a href="product-sticky.html">Sticky Info</a></li>
                                                        <li><a href="product-sidebar.html">Boxed With Sidebar</a></li>
                                                        <li><a href="product-fullwidth.html">Full Width</a></li>
                                                        <li><a href="product-masonry.html">Masonry Sticky Info</a></li>
                                                    </ul>
                                                </div><!-- End .menu-col -->
                                            </div><!-- End .col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="banner banner-overlay">
                                                    <a href="category.html">
                                                        <img src="assets/images/menu/banner-2.jpg" alt="Banner">

                                                        <div class="banner-content banner-content-bottom">
                                                            <div class="banner-title text-white">New Trends<br><span><strong>spring 2019</strong></span></div><!-- End .banner-title -->
                                                        </div><!-- End .banner-content -->
                                                    </a>
                                                </div><!-- End .banner -->
                                            </div><!-- End .col-md-6 -->
                                        </div><!-- End .row -->
                                    </div><!-- End .megamenu megamenu-sm -->
                                </li>
                                <li>
                                    <a href="#" class="sf-with-ul">IHURIRO</a>
                                </li>
                            </ul><!-- End .menu -->
                        </nav><!-- End .main-nav -->
                    </div><!-- End .header-left -->

                    <div class="header-right">
                        <div class="header-search">
                            <a href="#" class="search-toggle" role="button" title="Search"><i class="icon-search"></i></a>
                            <form action="#" method="get">
                                <div class="header-search-wrapper">
                                    <label for="q" class="sr-only">Search</label>
                                    <input type="search" class="form-control" name="q" id="q" placeholder="Search in..." required>
                                </div><!-- End .header-search-wrapper -->
                            </form>
                        </div><!-- End .header-search -->
                        <div class="dropdown cart-dropdown">
                        </div>
                        
                        
                    </div><!-- End .header-right -->
                </div><!-- End .container -->
            </div><!-- End .header-middle -->
        </header><!-- End .header -->
<div class="container text-center my-5">
    <h2 class="text-success mb-3">✅ Payment Complete</h2>
    <p>Thank you! Your payment has been received. We'll begin processing your order shortly.</p>
    <a href="{{ url('/') }}" class="btn btn-outline-primary mt-3">Return to Shop</a>
</div>

</body>
<footer class="footer">
    <div class="cta cta-horizontal cta-horizontal-box bg-dark bg-image" style="background-image: url('assets/images/demos/demo-14/bg-1.jpg');">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-xl-8 offset-xl-2">
                    <div class="row align-items-center">
                        <div class="col-lg-5 cta-txt">
                            <h3 class="cta-title text-primary">Join Our Newsletter</h3><!-- End .cta-title -->
                            <p class="cta-desc text-light">Subcribe to get information about products and coupons</p><!-- End .cta-desc -->
                        </div><!-- End .col-lg-5 -->
                        
                        <div class="col-lg-7">
                            <form action="#">
                                <div class="input-group">
                                    <input type="email" class="form-control" placeholder="Enter your Email Address" aria-label="Email Adress" required>
                                    <div class="input-group-append">
                                        <button class="btn" type="submit">Subscribe</button>
                                    </div><!-- .End .input-group-append -->
                                </div><!-- .End .input-group -->
                            </form>
                        </div><!-- End .col-lg-7 -->
                    </div><!-- End .row -->
                </div><!-- End .col-xl-8 offset-2 -->
            </div><!-- End .row -->
        </div><!-- End .container-fluid -->
    </div><!-- End .cta -->
    <div class="footer-middle border-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-lg-4">
                    <div class="widget widget-about">
                        
                        <img src="{{ asset('assets/images/demos/demo-14/logo-footer.png') }}" class="footer-logo" alt="Footer Logo" width="105" height="25">
                        <p>At The One Diva Ltd, we specialize in providing top-quality beauty services and premium beauty products. From makeup and spa treatments to nails and skincare, we ensure you look and feel your best. Explore our collection of beauty and fashion products, carefully selected to enhance your style</p>
                        
                        <div class="widget-about-info">
                            <div class="row">
                                <div class="col-sm-6 col-md-4">
                                    <span class="widget-about-title">Got Question? Call us 24/7</span>
                                    <a href="tel:0780159059">+250780159059</a>
                                </div><!-- End .col-sm-6 -->
                                <div class="col-sm-6 col-md-8">
                                    <span class="widget-about-title">Payment Method Coming Soon</span>
                                    <figure class="footer-payments">
                                        <img src="{{ asset('assets/images/payments.png') }}" alt="Payment methods" width="272" height="20">
                                    </figure><!-- End .footer-payments -->
                                </div><!-- End .col-sm-6 -->
                            </div><!-- End .row -->
                        </div><!-- End .widget-about-info -->
                    </div><!-- End .widget about-widget -->
                </div><!-- End .col-sm-12 col-lg-4 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">Useful Links</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="about.html">About</a></li>
                            <li><a href="#">How to shop </a></li>
                            <li><a href="faq.html">FAQ</a></li>
                            <li><a href="contact.html">Contact us</a></li>
                            <li><a href="login.html">Log in</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-2 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">Service</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="#">SPA</a></li>
                            <li><a href="#">Hairstyle</a></li>
                            <li><a href="#">Lashes</a></li>
                            <li><a href="#">Nails</a></li>
                            <li><a href="#">Barber Shop</a></li>
                            <li><a href="#">Tattoo</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-2 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget">
                        <h4 class="widget-title">My Account</h4><!-- End .widget-title -->

                        <ul class="widget-list">
                            <li><a href="#">Sign In</a></li>
                            <li><a href="cart.html">View Cart</a></li>
                            <li><a href="#">My Wishlist</a></li>
                            <li><a href="#">Track My Order</a></li>
                            <li><a href="#">Help</a></li>
                        </ul><!-- End .widget-list -->
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-2 -->

                <div class="col-sm-4 col-lg-2">
                    <div class="widget widget-newsletter">
                        <h4 class="widget-title">Sign Up to Newsletter</h4><!-- End .widget-title -->

                        <p>Subcribe to get information about products and coupons</p>
                        
                        <form action="#">
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Enter your Email Address" aria-label="Email Adress" required>
                                <div class="input-group-append">
                                    <button class="btn btn-dark" type="submit"><i class="icon-long-arrow-right"></i></button>
                                </div><!-- .End .input-group-append -->
                            </div><!-- .End .input-group -->
                        </form>
                    </div><!-- End .widget -->
                </div><!-- End .col-sm-4 col-lg-2 -->
            </div><!-- End .row -->
        </div><!-- End .container-fluid -->
    </div><!-- End .footer-middle -->

    <div class="footer-bottom">
        <div class="container-fluid">
            <p class="footer-copyright">
                Copyright © <span id="year"></span> Diva House Beauty. All Rights Reserved.  
                Designed by <strong>Izere Moubarak</strong>.
              </p><!-- End .footer-copyright -->
            <div class="social-icons social-icons-color">
                <span class="social-label">Social Media</span>
                <a href="#" class="social-icon social-facebook" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                <a href="#" class="social-icon social-twitter" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                <a href="#" class="social-icon social-instagram" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                <a href="#" class="social-icon social-youtube" title="Youtube" target="_blank"><i class="icon-youtube"></i></a>
                <a href="#" class="social-icon social-pinterest" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
            </div><!-- End .soial-icons -->
        </div><!-- End .container-fluid -->
    </div><!-- End .footer-bottom -->
</footer><!-- End .footer -->
</div><!-- End .page-wrapper -->
<button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>

<!-- Mobile Menu -->
<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

<div class="mobile-menu-container">
<div class="mobile-menu-wrapper">
    <span class="mobile-menu-close"><i class="icon-close"></i></span>
    
    <form action="#" method="get" class="mobile-search">
        <label for="mobile-search" class="sr-only">Search</label>
        <input type="search" class="form-control" name="mobile-search" id="mobile-search" placeholder="Search in..." required>
        <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
    </form>

    <ul class="nav nav-pills-mobile" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="mobile-menu-link" data-toggle="tab" href="#mobile-menu-tab" role="tab" aria-controls="mobile-menu-tab" aria-selected="true">Menu</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="mobile-cats-link" data-toggle="tab" href="#mobile-cats-tab" role="tab" aria-controls="mobile-cats-tab" aria-selected="false">Categories</a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="mobile-menu-tab" role="tabpanel" aria-labelledby="mobile-menu-link">
            <nav class="mobile-nav">
                <ul class="mobile-menu">
                    <li class="active">
                        <a href="{{route('home')}}">Home</a>
                    </li>
                    <li>
                        <a href="{{route('category')}}">Shop</a>
                    </li>
                    <li>
                        <a href="product.html" class="sf-with-ul">Service</a>
                        <ul>
                            <li><a href="product.html">Default</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">IHURIRO</a>
                        <ul>
                            <li>
                                <a href="about.html">About</a>

                                <ul>
                                    <li><a href="about.html">About 01</a></li>
                                    <li><a href="about-2.html">About 02</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="contact.html">Contact</a>

                                <ul>
                                    <li><a href="contact.html">Contact 01</a></li>
                                    <li><a href="contact-2.html">Contact 02</a></li>
                                </ul>
                            </li>
                            <li><a href="login.html">Login</a></li>
                            <li><a href="faq.html">FAQs</a></li>
                            <li><a href="404.html">Error 404</a></li>
                            <li><a href="coming-soon.html">Coming Soon</a></li>
                        </ul>
                    </li>
                </ul>
            </nav><!-- End .mobile-nav -->
        </div><!-- .End .tab-pane -->
        <div class="tab-pane fade" id="mobile-cats-tab" role="tabpanel" aria-labelledby="mobile-cats-link">
            <nav class="mobile-cats-nav">
                <ul class="mobile-cats-menu">
                    @foreach($categories as $category)
                        <li>
                            <a class="mobile-cats-lead" href="{{ route('category', $category->id) }}">
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                
            </nav><!-- End .mobile-cats-nav -->
        </div><!-- .End .tab-pane -->
    </div><!-- End .tab-content -->

    <div class="social-icons">
        <a href="#" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
        <a href="#" class="social-icon" target="_blank" title="Twitter"><i class="icon-twitter"></i></a>
        <a href="#" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
        <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a>
    </div><!-- End .social-icons -->
</div><!-- End .mobile-menu-wrapper -->
</div><!-- End .mobile-menu-container -->

<!-- Sign in / Register Modal -->
<div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="icon-close"></i></span>
            </button>

            <div class="form-box">
                <div class="form-tab">
                    <ul class="nav nav-pills nav-fill" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin" role="tab" aria-controls="signin" aria-selected="true">Sign In</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab" aria-controls="register" aria-selected="false">Register</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="tab-content-5">
                        <div class="tab-pane fade show active" id="signin" role="tabpanel" aria-labelledby="signin-tab">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="signin-email">Email *</label>
                                    <input type="email" class="form-control" id="signin-email" name="email" required>
                                </div>
                            
                                <div class="form-group">
                                    <label for="signin-password">Password *</label>
                                    <input type="password" class="form-control" id="signin-password" name="password" required>
                                </div>
                            
                                <div class="form-footer">
                                    <button type="submit" class="btn btn-outline-primary-2">
                                        <span>LOG IN</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>
                            
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="signin-remember" name="remember">
                                        <label class="custom-control-label" for="signin-remember">Remember Me</label>
                                    </div>
                            
                                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot Your Password?</a>
                                </div>
                            </form>                                    
                            <div class="form-choice">
                                <p class="text-center">or sign in with</p>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <a href="{{route('google-auth')}}" class="btn btn-login btn-g">
                                            <i class="icon-google"></i>
                                            Login With Google
                                        </a>
                                    </div><!-- End .col-6 -->
                                </div><!-- End .row -->
                            </div><!-- End .form-choice -->
                        </div><!-- .End .tab-pane -->
                        <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="register-name">Your Name *</label>
                                    <input type="text" class="form-control" id="register-name" name="name" required>
                                </div>
                            
                                <div class="form-group">
                                    <label for="register-email">Your Email *</label>
                                    <input type="email" class="form-control" id="register-email" name="email" required>
                                </div>
                            
                                <div class="form-group">
                                    <label for="register-password-2">Password *</label>
                                    <input type="password" class="form-control" id="register-password-2" name="password" required>
                                    <small id="password-help" class="form-text text-muted"></small> <!-- Help text for password strength -->
                                </div><!-- End .form-group -->
                                
                                                                   
                            
                                <div class="form-group">
                                    <label for="register-password-confirm">Confirm Password *</label>
                                    <input type="password" class="form-control" id="register-password-confirm" name="password_confirmation" required>
                                </div>
                            
                                <div class="form-footer">
                                    <button type="submit" class="btn btn-outline-primary-2">
                                        <span>SIGN UP</span>
                                        <i class="icon-long-arrow-right"></i>
                                    </button>
                            
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="register-policy" required>
                                        <label class="custom-control-label" for="register-policy">I agree to the <a href="#">privacy policy</a> *</label>
                                    </div>
                                </div>
                            </form>                                    
                            <div class="form-choice">
                                <p class="text-center">or sign in with</p>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <a href="{{route('google-auth')}}" class="btn btn-login btn-g">
                                            <i class="icon-google"></i>
                                            Login With Google
                                        </a>
                                    </div><!-- End .col-6 -->
                                </div><!-- End .row -->
                            </div><!-- End .form-choice -->
                        </div><!-- .End .tab-pane -->
                    </div><!-- End .tab-content -->
                </div><!-- End .form-tab -->
            </div><!-- End .form-box -->
        </div><!-- End .modal-body -->
    </div><!-- End .modal-content -->
</div><!-- End .modal-dialog -->
</div><!-- End .modal -->

<!-- Plugins JS File -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.hoverIntent.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('assets/js/superfish.min.js') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-input-spinner.js') }}"></script>
<script src="{{ asset('assets/js/jquery.elevateZoom.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap-input-spinner.js') }}"></script>
<script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
<!-- Main JS File -->
<script src="{{ asset('assets/js/main.js') }}">
setTimeout(function() {
let alert = document.querySelector('.alert');
if (alert) {
let bsAlert = new bootstrap.Alert(alert);
bsAlert.close();
}
}, 3000); // 3000ms = 3 seconds
</script>

<script>
$(document).ready(function() {
@if($errors->any())
$('#signin-modal').modal('show');
@endif
});
document.getElementById("register-password-2").addEventListener("input", function () {
let password = this.value;
let helpText = document.getElementById("password-help");
let strongRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/;

if (strongRegex.test(password)) {
helpText.style.color = "#28a745"; // Green for success
helpText.textContent = "✅ Strong password!";
} else {
helpText.style.color = "#dc3545"; // Red for error
helpText.textContent = "⚠️ Password must be at least 8 characters long, including an uppercase letter, a lowercase letter, a number, and a special character.";
}
});
</script>
<script>
    function previewImage(event) {
        const imagePreview = document.getElementById('imagePreview');
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@if(session('error'))
<script>
alert("{{ session('error') }}"); // Shows an alert popup
</script>
@endif  
</body>
</html>

