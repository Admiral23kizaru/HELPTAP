<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to HelpTap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            /* Background image with gradient overlay */
            background: linear-gradient(rgba(116,235,213,0.7), rgba(172,182,229,0.7)),
                url('https://images.unsplash.com/photo-1465101178521-c1a9136a3b99?auto=format&fit=crop&w=1500&q=80') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Subtle blur overlay for background */
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            backdrop-filter: blur(2.5px);
        }
        .card {
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 430px;
            width: 100%;
            background: rgba(255,255,255,0.97);
            border: none;
            text-align: center;
            position: relative;
            z-index: 1;
            opacity: 0;
            transform: translateY(40px);
            animation: fadeInUp 1.1s cubic-bezier(.23,1.01,.32,1) 0.2s forwards;
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: none;
            }
        }
        .header-img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto 1.2rem auto;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            display: block;
            position: relative;
            top: -40px;
            background: #fff;
            border: 6px solid #fff;
        }
        .card-content {
            margin-top: -30px;
        }
        .btn-primary {
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 12px 0;
            border-radius: 8px;
            background: linear-gradient(90deg, #74ebd5 0%, #ACB6E5 100%);
            border: none;
            box-shadow: 0 2px 8px rgba(116,235,213,0.15);
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .btn-primary:focus, .btn-primary:hover {
            box-shadow: 0 4px 16px rgba(116,235,213,0.25);
            transform: translateY(-2px) scale(1.03);
        }
        .btn-outline-secondary {
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 12px 0;
            border-radius: 8px;
            margin-left: 0.5rem;
        }
        .tagline {
            font-size: 1.1rem;
            color: #5a5a5a;
            margin-bottom: 1.2rem;
            font-style: italic;
        }
        .footer {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            width: 100%;
            background: rgba(255,255,255,0.85);
            text-align: center;
            padding: 0.6rem 0 0.3rem 0;
            font-size: 0.98rem;
            z-index: 10;
            box-shadow: 0 -2px 12px rgba(31,38,135,0.07);
        }
        .footer a {
            color: #74ebd5;
            text-decoration: none;
            margin: 0 0.7rem;
            transition: color 0.2s;
        }
        .footer a:hover {
            color: #ACB6E5;
        }
        @media (max-width: 500px) {
            .card { padding: 1.2rem 0.5rem; }
            .header-img { width: 80px; height: 80px; top: -30px; }
            .footer { font-size: 0.92rem; }
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card" role="main" aria-label="Welcome card">
            <img src="https://images.unsplash.com/photo-1503676382389-4809596d5290?auto=format&fit=facearea&w=400&h=400&q=80" alt="Helping Each Other" class="header-img" loading="lazy">
            <div class="card-content">
                <h1 class="mb-2 fw-bold">Welcome to HelpTap</h1>
                <div class="tagline">Empowering communities, one tap at a time.</div>
                <p class="mb-4 text-muted">A platform to request and offer help in your community.<br>Join as a Requester, Helper, or Admin.</p>
                <hr>
                <div class="d-flex justify-content-center flex-wrap gap-2">
                    <a href="includes/signup.php" class="btn btn-primary px-4 py-2 fw-semibold" aria-label="Sign up for HelpTap"><i class="fa-solid fa-user-plus me-2"></i>Sign up for HelpTap</a>
                    <a href="includes/login.php" class="btn btn-outline-secondary px-4 py-2 fw-semibold" aria-label="Login to HelpTap"><i class="fa-solid fa-right-to-bracket me-2"></i>Login</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="admin-login.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</body>
</html> 